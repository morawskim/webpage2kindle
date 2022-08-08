<?php

namespace App\Job;

use App\Job\Command\CreateJobCommand;
use App\Job\Command\SetPushToKindleUrlCommand;
use App\Job\Command\SetWebPageContentCommand;
use App\Job\Domain\Job;
use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Job\PageContentFetcher\PageContentFetcherInterface;
use App\Job\PushToKindleUrlGenerator\PushToKindleUrlGeneratorInterface;
use Broadway\CommandHandling\CommandBus;

class PushToKindlePipelineService
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly CommandBus $commandBus,
        private readonly PageContentFetcherInterface $pageContentFetcher,
        private readonly PushToKindleUrlGeneratorInterface $kindleUrlGenerator,
    ) {
    }

    public function addNewJob(JobId $jobId, string $url): void
    {
        $command = new CreateJobCommand($jobId, $url);
        $this->commandBus->dispatch($command);
    }

    public function fetchPageContentForJob(JobId $jobId): void
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($jobId);

        $content = $this->pageContentFetcher->getPageContent($job->getUrlToFetch());
        $this->commandBus->dispatch(new SetWebPageContentCommand($jobId, $content));
    }

    public function createPushToKindleUrl(JobId $jobId): string
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($jobId);
        $kindleUrl = $this->kindleUrlGenerator->createUrl($job);
        $this->commandBus->dispatch(new SetPushToKindleUrlCommand($jobId, $kindleUrl));

        return $kindleUrl;
    }
}
