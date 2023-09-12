<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

class NewJob extends BaseEvent
{
    public function __construct(public readonly JobId $jobId)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'new_job',
            'data' => [
                'jobId' => (string) $this->jobId,
            ]
        ];
    }
}
