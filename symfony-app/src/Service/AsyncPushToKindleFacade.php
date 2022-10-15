<?php

namespace App\Service;

use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class AsyncPushToKindleFacade
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly Producer $producer
    ) {
    }

    public function publishNewJob(string $url): JobId
    {
        $jobId = $this->jobRepository->createNextJobId();

        $this->producer->publish(json_encode(['jobId' => (string) $jobId, 'url' => $url]));

        return $jobId;
    }
}
