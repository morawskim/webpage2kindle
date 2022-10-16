<?php

namespace App\Web\Domain\Contract;

use App\Job\Domain\JobId;

interface NewJobProducerInterface
{
    public function publishNewJob(string $url): JobId;
}
