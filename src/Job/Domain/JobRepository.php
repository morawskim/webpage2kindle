<?php

namespace App\Job\Domain;

use App\Job\Event\EventHumanDescriptionInterface;
use Broadway\Domain\DomainMessage;
use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Broadway\UuidGenerator\UuidGeneratorInterface;

class JobRepository extends EventSourcingRepository
{
    private UuidGeneratorInterface $uuidGenerator;
    private EventStore $eventStore;

    public function __construct(
        EventStore $eventStore,
        EventBus $eventBus,
        UuidGeneratorInterface $uuidGenerator,
        array $eventStreamDecorators = []
    ) {
        parent::__construct(
            $eventStore,
            $eventBus,
            Job::class,
            new PublicConstructorAggregateFactory(),
            $eventStreamDecorators
        );

        $this->uuidGenerator = $uuidGenerator;
        $this->eventStore = $eventStore;
    }

    public function createNextJobId(): JobId
    {
        return new JobId($this->uuidGenerator->generate());
    }

    /**
     * @return JobChangelog[]
     */
    public function getJobDetailsAsStream(JobId $jobId): array
    {
        $records = [];

        /** @var DomainMessage[] $domainEventStream */
        $domainEventStream = $this->eventStore->load($jobId);
        foreach ($domainEventStream as $item) {
            $payload = $item->getPayload();

            if ($payload instanceof EventHumanDescriptionInterface) {
                $records[] = new JobChangelog($payload->humanDescription(), $item->getRecordedOn()->toNative());
            } else {
                throw new \RuntimeException(sprintf(
                'Event "%s" for  ob "%s" does not implement "%s"',
                    $item->getType(),
                    $jobId,
                    EventHumanDescriptionInterface::class,
                ));
            }
        }

        return $records;
    }
}
