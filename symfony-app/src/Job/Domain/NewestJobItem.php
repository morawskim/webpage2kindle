<?php

namespace App\Job\Domain;

class NewestJobItem
{
    public function __construct(
        public readonly JobId $jobId,
        public readonly string $url,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }
}
