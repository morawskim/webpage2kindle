<?php

namespace App\Service;

use App\Job\Domain\JobId;
use App\Web\Domain\Contract\NewJobProducerInterface;

class AsyncPushToKindleFacade
{
    public function __construct(
        private readonly NewJobProducerInterface $newJobProducer,
    ) {
    }

    public function publishNewJob(string $url): JobId
    {
        return $this->newJobProducer->publishNewJob($url);
    }
}
