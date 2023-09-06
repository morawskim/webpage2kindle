<?php

namespace App\Job\Domain;

use Broadway\Domain\AggregateRoot;
use Broadway\Repository\AggregateNotFoundException;

class InMemoryJobProvider implements JobProviderInterface
{
    /** @var array<string, Job>  */
    private array $data = [];

    public function getJob(string $id): AggregateRoot
    {
        return $this->data[$id] ?? throw new AggregateNotFoundException(
            sprintf('Job with id "%s" not found', $id)
        );
    }

    public function appendJob(Job $job): void
    {
        $this->data[$job->getAggregateRootId()] = $job;
    }

    public function getJobDetailsAsStream(JobId $jobId): array
    {
        throw new \RuntimeException('unsupported in this implementation');
    }
}
