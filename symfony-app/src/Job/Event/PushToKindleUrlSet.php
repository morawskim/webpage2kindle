<?php

namespace App\Job\Event;

use App\Job\Domain\JobId;
use Broadway\Serializer\Serializable;

/**
 * @phpstan-type EventArray array{jobId: string, url: string}
 */
class PushToKindleUrlSet implements Serializable, EventHumanDescriptionInterface
{
    public function __construct(private readonly JobId $jobId, private readonly string $url)
    {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return EventArray $data
     */
    public function serialize(): array
    {
        return ['jobId' => (string) $this->jobId, 'url' => $this->url];
    }

    /**
     * @param EventArray $data
     */
    public static function deserialize(array $data): self
    {
        return new self(new JobId($data['jobId']), $data['url']);
    }

    public function humanDescription(): string
    {
        return 'A PushToKindle url has been set - ' . $this->url;
    }
}
