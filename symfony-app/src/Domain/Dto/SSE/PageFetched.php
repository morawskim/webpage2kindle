<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

/**
 * @phpstan-import-type EventAsArray from BaseEvent
 */
class PageFetched extends BaseEvent
{
    public function __construct(private readonly JobId $jobId)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    /**
     * @return EventAsArray
     */
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
