<?php

namespace App\Job\Domain;

class JobChangelog
{
    public function __construct(
        public readonly string $message,
        public readonly \DateTimeImmutable $createdAt,
    ) {
    }
}
