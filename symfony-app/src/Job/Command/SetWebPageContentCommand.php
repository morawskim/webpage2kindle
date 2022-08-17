<?php

namespace App\Job\Command;

use App\Job\Domain\JobId;

class SetWebPageContentCommand
{
    public function __construct(private readonly JobId $jobId, private readonly string $content)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
