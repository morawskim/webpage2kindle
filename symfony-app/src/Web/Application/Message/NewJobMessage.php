<?php

namespace App\Web\Application\Message;

use App\Job\Domain\JobId;

class NewJobMessage
{
    public function __construct(
        public readonly JobId $jobId,
        public readonly string $url,
    ) {
    }
}
