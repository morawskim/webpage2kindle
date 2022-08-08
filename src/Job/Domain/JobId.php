<?php

namespace App\Job\Domain;

final class JobId
{
    public function __construct(private readonly string $jobId)
    {
    }

    public function __toString(): string
    {
        return $this->jobId;
    }
}
