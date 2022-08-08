<?php

namespace App\Job\Event;

use App\Job\Domain\JobId;
use Broadway\Serializer\Serializable;

class WebPageContentUpdated implements Serializable
{
    public function __construct(private readonly JobId $jobId, private readonly string $content)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public static function deserialize(array $data): self
    {
        return new self(new JobId($data['jobId']), $data['content']);
    }

    public function serialize(): array
    {
        return [
            'jobId' => (string) $this->jobId,
            'content' => $this->content,
        ];
    }
}
