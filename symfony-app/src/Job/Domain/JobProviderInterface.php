<?php

namespace App\Job\Domain;

use Broadway\Domain\AggregateRoot;
use Broadway\Repository\AggregateNotFoundException;

interface JobProviderInterface
{
    /**
     * @throws AggregateNotFoundException
     */
    public function getJob(string $id): AggregateRoot;

    /**
     * @return JobChangelog[]
     */
    public function getJobDetailsAsStream(JobId $jobId): array;
}
