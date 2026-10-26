<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Job\Domain\JobId;
use App\Web\Application\Message\CreatePushToKindleUrlMessage;
use App\Web\Domain\Contract\PushToKindleProducerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class PushToKindleProducer implements PushToKindleProducerInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function publishPushToKindle(JobId $jobId): void
    {
        $this->messageBus->dispatch(new CreatePushToKindleUrlMessage($jobId));
    }
}
