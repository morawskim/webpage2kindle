<?php

namespace App\Web\Domain\Contract;

use App\Job\Domain\JobId;

interface FetchPageContentProducerInterface
{
    public function publishFetchPageContent(JobId $jobId): void;
}
