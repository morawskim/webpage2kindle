<?php

namespace App\Web\Application\Message;

use App\Job\Domain\JobId;

class FetchPageContentMessage
{
    public function __construct(
        public readonly JobId $jobId,
    ) {
    }
}
