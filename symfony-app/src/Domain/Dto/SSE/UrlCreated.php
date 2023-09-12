<?php

namespace App\Domain\Dto\SSE;

use App\Job\Domain\JobId;

class UrlCreated extends BaseEvent
{
    public function __construct(private readonly JobId $jobId, private readonly string $url)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

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
