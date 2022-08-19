<?php

namespace App\Job\Domain;

use App\Job\Event\JobMarkedAsFailed;
use App\Job\Event\JobToProcessWebPageWasCreated;
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
    private string $failedReason = '';

    public static function newJob(JobId $jobId, string $urlToFetch): self
    {
        $job = new self();
        $job->new($jobId, $urlToFetch);

        return $job;
    }

    public static function newJobToProcessPage(JobId $jobId, string $url, string $webPageContent): self
    {
        $job = new self();
        $job->newJobToProcessWebPage($jobId, $url, $webPageContent);

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

    private function newJobToProcessWebPage(JobId $jobId, string $url, string $webPageContent): void
    {
        $this->apply(
            new JobToProcessWebPageWasCreated($jobId, $url, $webPageContent)
        );
    }

    public function setWebPageContent(string $content): void
    {
        $this->apply(new WebPageContentUpdated(
            $this->jobId,
            $content
        ));
    }

    public function markAsFailed(string $reason): void
    {
        $this->apply(new JobMarkedAsFailed($this->jobId, $reason));
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

    protected function applyJobToProcessWebPageWasCreated(JobToProcessWebPageWasCreated $event): void
    {
        $this->jobId = $event->getJobId();
        $this->urlToFetch = $event->getUrl();
        $this->webPageContent = $event->getWebPageContent();
    }

    protected function applyWebPageContentUpdated(WebPageContentUpdated $event): void
    {
        $this->webPageContent = $event->getContent();
    }

    protected function applyJobMarkedAsFailed(JobMarkedAsFailed $event): void
    {
        $this->failedReason = $event->getReason();
    }

    protected function applyPushToKindleUrlSet(PushToKindleUrlSet $event): void
    {
        $this->pushToKindleUrl = $event->getUrl();
    }
}
