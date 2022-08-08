<?php

namespace App\Job\Domain;

use Broadway\EventHandling\EventBus;
use Broadway\EventSourcing\AggregateFactory\PublicConstructorAggregateFactory;
use Broadway\EventSourcing\EventSourcingRepository;
use Broadway\EventStore\EventStore;
use Broadway\UuidGenerator\UuidGeneratorInterface;

class JobRepository extends EventSourcingRepository
{
    private UuidGeneratorInterface $uuidGenerator;

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
    }

    public function createNextJobId(): JobId
    {
        return new JobId($this->uuidGenerator->generate());
    }
}
