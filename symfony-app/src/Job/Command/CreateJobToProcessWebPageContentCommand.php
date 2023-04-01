<?php

namespace App\Job\Command;

use App\Job\Domain\JobId;

class CreateJobToProcessWebPageContentCommand
{
    public function __construct(
        private readonly JobId $jobId,
        private readonly string $url,
        private readonly string $webPageContent,
        private readonly string $title,
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

    public function getTitle(): string
    {
        return $this->title;
    }
}
