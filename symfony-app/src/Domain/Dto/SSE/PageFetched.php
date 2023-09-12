<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

class PageFetched extends BaseEvent
{
    public function __construct(private readonly JobId $jobId)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => 'page_fetched',
            'data' => [
                'jobId' => (string) $this->jobId,
            ]
        ];
    }
}
