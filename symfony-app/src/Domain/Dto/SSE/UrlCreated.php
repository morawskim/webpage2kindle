<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

/**
 * @phpstan-import-type EventAsArray from BaseEvent
 */
class UrlCreated extends BaseEvent
{
    public function __construct(private readonly JobId $jobId, private readonly string $url)
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
            'type' => 'url_created',
            'data' => [
                'jobId' => (string) $this->jobId,
                'url' => $this->url,
            ]
        ];
    }
}
