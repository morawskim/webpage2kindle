<?php

namespace App\Job\Command;

use App\Job\Domain\JobId;

class MarkJobAsFailedCommand
{
    public function __construct(private readonly JobId $jobId, private readonly string $reason)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }


    public function getReason(): string
    {
        return $this->reason;
    }
}
