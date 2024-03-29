<?php

namespace App\Service;

use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Job\Exception\CannotGetPageContentException;
use App\Job\PushToKindlePipelineService;

class SynchronousPushToKindleFacade
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly JobRepository $jobRepository
    ) {
    }

    /**
     * @throws CannotGetPageContentException
     */
    public function run(string $url): string
    {
        $jobId = $this->jobRepository->createNextJobId();
        $this->pushToKindlePipeline->addNewJob($jobId, $url);
        $this->pushToKindlePipeline->fetchPageContentForJob($jobId);

        return $this->pushToKindlePipeline->createPushToKindleUrl($jobId);
    }

    public function processWebPageContent(string $url, string $webPageContent, string $title): string
    {
        $jobId = $this->jobRepository->createNextJobId();
        $this->pushToKindlePipeline->addNewJobToProcessBody($jobId, $url, $webPageContent, $title);
        $this->pushToKindlePipeline->createReadableVersionOfContent($jobId);

        return $this->pushToKindlePipeline->createPushToKindleUrl($jobId);
    }
}
