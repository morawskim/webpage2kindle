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

    public function getJobDetailsAsStream(JobId $jobId): array;
}
