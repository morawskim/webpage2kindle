<?php

namespace App\Job\Event;

use App\Job\Domain\JobId;
use Broadway\Serializer\Serializable;

/**
 * @phpstan-type EventArray array{jobId: string, content: string}
 */
class WebPageContentUpdated implements Serializable, EventHumanDescriptionInterface
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

    /**
     * @param EventArray $data
     */
    public static function deserialize(array $data): self
    {
        return new self(new JobId($data['jobId']), $data['content']);
    }

    /**
     * @return EventArray $data
     */
    public function serialize(): array
    {
        return [
            'jobId' => (string) $this->jobId,
            'content' => $this->content,
        ];
    }

    public function humanDescription(): string
    {
        return sprintf('A webpage content has been fetched. Total bytes "%d"', strlen($this->content));
    }
}
