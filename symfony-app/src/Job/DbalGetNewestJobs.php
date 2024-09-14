<?php

namespace App\Job;

use App\Job\Domain\GetNewestJobsInterface;
use App\Job\Domain\NewestJobItem;
use App\Job\Event\JobToProcessWebPageWasCreated;
use App\Job\Event\JobWasCreated;
use Broadway\Domain\DateTime;
use Broadway\Domain\DomainMessage;
use Broadway\EventStore\CallableEventVisitor;
use Broadway\EventStore\EventVisitor;
use Broadway\EventStore\Exception\InvalidIdentifierException;
use Broadway\EventStore\Management\Criteria;
use Broadway\EventStore\Management\CriteriaNotSupportedException;
use Broadway\Serializer\Serializer;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

class DbalGetNewestJobs implements GetNewestJobsInterface
{
    public function __construct(
        private readonly Connection $connection,
        private readonly Serializer $payloadSerializer,
        private readonly Serializer $metadataSerializer,
        private readonly bool $useBinary,
        private readonly string $tableName = 'events',
    ) {
    }

    public function getNewestJobs(): array
    {
        $acceptedInvitesCriteria = Criteria::create()
            ->withEventTypes([
                str_replace('\\', '.', JobWasCreated::class),
                str_replace('\\', '.', JobToProcessWebPageWasCreated::class),
            ]);

        $data = [];
        $this->visitEvents($acceptedInvitesCriteria, new CallableEventVisitor(function (DomainMessage $domainMessage) use (&$data) {
            /** @var JobWasCreated|JobToProcessWebPageWasCreated $payload */
            $payload = $domainMessage->getPayload();
            $data[] = new NewestJobItem(
                $payload->getJobId(),
                $payload instanceof JobWasCreated ? $payload->getUrlToFetch() : $payload->getUrl(),
                $domainMessage->getRecordedOn()->toNative()
            );
        }));

        return $data;
    }

    private function visitEvents(Criteria $criteria, EventVisitor $eventVisitor): void
    {
        $result = $this->prepareVisitEventsStatement($criteria);

        while ($row = $result->fetchAssociative()) {
            $domainMessage = $this->deserializeEvent($row);

            $eventVisitor->doWithEvent($domainMessage);
        }
    }

    private function prepareVisitEventsStatement(Criteria $criteria): Result
    {
        list($where, $bindValues, $bindValueTypes) = $this->prepareVisitEventsStatementWhereAndBindValues($criteria);
        $query = 'SELECT uuid, playhead, metadata, payload, recorded_on
            FROM '.$this->tableName.'
            '.$where.'
            ORDER BY id DESC LIMIT 0, 50';

        return $this->connection->executeQuery($query, $bindValues, $bindValueTypes);
    }

    /**
     * @phpstan-ignore-next-line
     */
    private function prepareVisitEventsStatementWhereAndBindValues(Criteria $criteria): array
    {
        if ($criteria->getAggregateRootTypes()) {
            throw new CriteriaNotSupportedException('DBAL implementation cannot support criteria based on aggregate root types.');
        }

        $bindValues = [];
        $bindValueTypes = [];

        $criteriaTypes = [];

        if ($criteria->getAggregateRootIds()) {
            $criteriaTypes[] = 'uuid IN (:uuids)';

            if ($this->useBinary) {
                $bindValues['uuids'] = [];
                foreach ($criteria->getAggregateRootIds() as $id) {
                    // @phpstan-ignore-next-line
                    $bindValues['uuids'][] = $this->convertIdentifierToStorageValue($id);
                }
                $bindValueTypes['uuids'] = ArrayParameterType::STRING;
            } else {
                $bindValues['uuids'] = $criteria->getAggregateRootIds();
                $bindValueTypes['uuids'] = ArrayParameterType::STRING;
            }
        }

        if ($criteria->getEventTypes()) {
            $criteriaTypes[] = 'type IN (:types)';
            $bindValues['types'] = $criteria->getEventTypes();
            $bindValueTypes['types'] = ArrayParameterType::STRING;
        }

        if (!$criteriaTypes) {
            return ['', [], []];
        }

        $where = 'WHERE '.join(' AND ', $criteriaTypes);

        return [$where, $bindValues, $bindValueTypes];
    }

    /**
     * @param mixed $id
     *
     * @return mixed
     */
    private function convertStorageValueToIdentifier($id)
    {
        if ($this->useBinary) {
            try {
                // @phpstan-ignore-next-line
                return $this->binaryUuidConverter::fromBytes($id);
            } catch (\Exception $e) {
                throw new InvalidIdentifierException('Could not convert binary storage value to UUID.');
            }
        }

        return $id;
    }

    /**
     * @param array{uuid: string, metadata: string, payload: string, recorded_on: string, playhead: int|string} $row
     *
     * @return DomainMessage
     */
    private function deserializeEvent(array $row): DomainMessage
    {
        return new DomainMessage(
            $this->convertStorageValueToIdentifier($row['uuid']),
            (int) $row['playhead'],
            $this->metadataSerializer->deserialize(json_decode($row['metadata'], true)),
            $this->payloadSerializer->deserialize(json_decode($row['payload'], true)),
            DateTime::fromString($row['recorded_on'])
        );
    }
}
