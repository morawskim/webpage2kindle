<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

/**
 * @phpstan-import-type EventAsArray from BaseEvent
 */
class NewJob extends BaseEvent
{
    public function __construct(public readonly JobId $jobId)
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
            'type' => 'new_job',
            'data' => [
                'jobId' => (string) $this->jobId,
            ]
        ];
    }
}
