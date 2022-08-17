<?php

namespace App\Job\Command;

use App\Job\Domain\JobId;

class SetPushToKindleUrlCommand
{
    public function __construct(private readonly JobId $jobId, private readonly string $url)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
