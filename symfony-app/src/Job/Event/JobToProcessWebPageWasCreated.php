<?php

namespace App\Job\Event;

use App\Job\Domain\JobId;
use Broadway\Serializer\Serializable;

class JobToProcessWebPageWasCreated implements Serializable, EventHumanDescriptionInterface
{
    public function __construct(
        private readonly JobId $jobId,
        private readonly string $url,
        private readonly string $webPageContent
    ) {
    }

    public function getJobId(): JobId
    {
        return $this->jobId;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWebPageContent(): string
    {
        return $this->webPageContent;
    }

    public function serialize(): array
    {
        return ['jobId' => (string) $this->jobId, 'webPageContent' => $this->webPageContent, 'url' => $this->url];
    }

    public static function deserialize(array $data): self
    {
        return new self(new JobId($data['jobId']), $data['url'], $data['webPageContent']);
    }

    public function humanDescription(): string
    {
        return sprintf('A new job has been created to process web page content ("%s")', $this->url);
    }
}
