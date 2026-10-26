<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Job\Domain\JobId;
use App\Web\Application\Message\FetchPageContentMessage;
use App\Web\Domain\Contract\FetchPageContentProducerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class FetchPageContentProducer implements FetchPageContentProducerInterface
{
    public function __construct(
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    public function publishFetchPageContent(JobId $jobId): void
    {
        $this->messageBus->dispatch(new FetchPageContentMessage($jobId));
    }
}
