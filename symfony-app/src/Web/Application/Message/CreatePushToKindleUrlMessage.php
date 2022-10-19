<?php

namespace App\Web\Application\Message;

use App\Job\Domain\JobId;

class CreatePushToKindleUrlMessage
{
    public function __construct(public readonly JobId $jobId)
    {
    }
}
