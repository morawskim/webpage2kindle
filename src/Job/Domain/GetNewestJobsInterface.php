<?php

namespace App\Job\Domain;

interface GetNewestJobsInterface
{
    /**
     * @return NewestJobItem[]
     */
    public function getNewestJobs(): array;
}
