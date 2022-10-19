<?php

namespace App\Web\Domain\Contract;

use App\Job\Domain\JobId;

interface PushToKindleProducerInterface
{
    public function publishPushToKindle(JobId $jobId): void;
}
