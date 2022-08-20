<?php

namespace App\Job;

use App\Job\Command\CreateJobCommand;
use App\Job\Command\CreateJobToProcessWebPageContentCommand;
use App\Job\Command\MarkJobAsFailedCommand;
use App\Job\Command\SetPushToKindleUrlCommand;
use App\Job\Command\SetWebPageContentCommand;
use App\Job\Domain\Job;
use App\Job\Domain\JobId;
use App\Job\Domain\JobRepository;
use App\Job\Exception\CannotCreateReadableVersionException;
use App\Job\Exception\CannotGetPageContentException;
use App\Job\PageContentFetcher\PageContentFetcherInterface;
use App\Job\PushToKindleUrlGenerator\PushToKindleUrlGeneratorInterface;
use App\Job\ReadablePageContent\CreateReadablePageContentInterface;
use Broadway\CommandHandling\CommandBus;
use Psr\Log\LoggerInterface;

class PushToKindlePipelineService
{
    public function __construct(
        private readonly JobRepository $jobRepository,
        private readonly CommandBus $commandBus,
        private readonly PageContentFetcherInterface $pageContentFetcher,
        private readonly PushToKindleUrlGeneratorInterface $kindleUrlGenerator,
        private readonly LoggerInterface $logger,
        private readonly CreateReadablePageContentInterface $createReadablePageContent,
    ) {
    }

    public function addNewJob(JobId $jobId, string $url): void
    {
        $command = new CreateJobCommand($jobId, $url);
        $this->commandBus->dispatch($command);
    }

    public function addNewJobToProcessBody(JobId $jobId, string$url, string $body): void
    {
        $command = new CreateJobToProcessWebPageContentCommand($jobId, $url, $body);
        $this->commandBus->dispatch($command);
    }

    /**
     * @throws CannotGetPageContentException
     */
    public function fetchPageContentForJob(JobId $jobId): void
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($jobId);
        try {
            $content = $this->pageContentFetcher->getPageContent($job->getUrlToFetch());
            $this->commandBus->dispatch(new SetWebPageContentCommand($jobId, $content));
        } catch (CannotGetPageContentException $e) {
            $this->commandBus->dispatch(new MarkJobAsFailedCommand($jobId, $e->getMessage()));
            $this->logger->error($e);
            throw $e;
        }
    }

    public function createReadableVersionOfContent(JobId $jobId): void
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($jobId);
        try {
            $body = $this->createReadablePageContent->createReadableVersionOfWebPageContent(
                $job->getWebPageContent(),
                $job->getUrlToFetch()
            );
            $this->commandBus->dispatch(new SetWebPageContentCommand($jobId, $body));
        } catch (CannotCreateReadableVersionException $e) {
            $this->commandBus->dispatch(new MarkJobAsFailedCommand($jobId, $e->getMessage()));
            $this->logger->error($e);
            throw $e;
        }
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
