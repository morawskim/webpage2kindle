<?php

namespace App\Job\Domain;

use App\Job\Event\JobWasCreated;
use App\Job\Event\PushToKindleUrlSet;
use App\Job\Event\WebPageContentUpdated;
use Broadway\EventSourcing\EventSourcedAggregateRoot;

class Job extends EventSourcedAggregateRoot
{
    private JobId $jobId;
    private string $urlToFetch;
    private string $webPageContent = '';
    private string $pushToKindleUrl = '';

    public static function newJob(JobId $jobId, string $urlToFetch): self
    {
        $job = new self();
        $job->new($jobId, $urlToFetch);

        return $job;
    }

    public function getAggregateRootId(): string
    {
        return (string) $this->jobId;
    }

    public function getUrlToFetch(): string
    {
        return $this->urlToFetch;
    }

    public function getWebPageContent(): string
    {
        return $this->webPageContent;
    }

    private function new(JobId $jobId, string $urlToFetch): void
    {
        $this->apply(
            new JobWasCreated($jobId, $urlToFetch)
        );
    }

    public function setWebPageContent(string $content): void
    {
        $this->apply(new WebPageContentUpdated(
            $this->jobId,
            $content
        ));
    }

    public function setPushToKindleUrl(string $url): void
    {
        $this->apply(new PushToKindleUrlSet(
            $this->jobId,
            $url
        ));
    }

    protected function applyJobWasCreated(JobWasCreated $event): void
    {
        $this->jobId = $event->getJobId();
        $this->urlToFetch = $event->getUrlToFetch();
    }

    protected function applyWebPageContentUpdated(WebPageContentUpdated $event): void
    {
        $this->webPageContent = $event->getContent();
    }

    protected function applyPushToKindleUrlSet(PushToKindleUrlSet $event): void
    {
        $this->pushToKindleUrl = $event->getUrl();
    }
}
