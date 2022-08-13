<?php

namespace App\Job\Event;

use App\Job\Domain\JobId;
use Broadway\Serializer\Serializable;

class JobMarkedAsFailed implements Serializable
{
    public function __construct(private readonly JobId $jobId, private readonly string $reason)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public static function deserialize(array $data): self
    {
        return new self(new JobId($data['jobId']), $data['reason']);
    }

    public function serialize(): array
    {
        return [
            'jobId' => (string) $this->jobId,
            'reason' => $this->reason,
        ];
    }
}
