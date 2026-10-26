<?php

namespace App\Web\Infrastructure\Producer\RabbitMq;

use App\Domain\Contract\SSEPublisherInterface;
use App\Domain\Dto\SSE\NewJob;
use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Web\Application\Message\NewJobMessage;
use App\Web\Domain\Contract\NewJobProducerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NewJobProducer implements NewJobProducerInterface
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly MessageBusInterface $messageBus,
        private readonly SSEPublisherInterface $SSEPublisher,
    ) {
    }

    public function publishNewJob(string $url): JobId
    {
        $jobId = $this->jobRepository->createNextJobId();
        $dto = new NewJobMessage($jobId, $url);

        $this->messageBus->dispatch($dto);
        $this->SSEPublisher->publish(new NewJob($jobId));

        return $jobId;
    }
}
