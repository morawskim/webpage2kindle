<?php

namespace App\Job\Event;

use App\Job\Domain\JobId;
use Broadway\Serializer\Serializable;

class JobWasCreated implements Serializable, EventHumanDescriptionInterface
{
    public function __construct(private readonly JobId $jobId, private readonly string $urlToFetch)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getUrlToFetch(): string
    {
        return $this->urlToFetch;
    }

    public function serialize(): array
    {
        return ['jobId' => (string) $this->jobId, 'urlToFetch' => $this->urlToFetch];
    }

    public static function deserialize(array $data): self
    {
        return new self(new JobId($data['jobId']), $data['urlToFetch']);
    }

    public function humanDescription(): string
    {
        return sprintf('A new job has been created to fetch content of "%s"', $this->urlToFetch);
    }
}
