<?php

namespace App\Job;

use App\Job\Command\CreateJobToProcessWebPageContentCommand;
use App\Job\Command\MarkJobAsFailedCommand;
use App\Job\Command\SetWebPageContentCommand;
use App\Job\Command\CreateJobCommand;
use App\Job\Command\SetPushToKindleUrlCommand;
use App\Job\Domain\Job;
use App\Job\Domain\JobRepository;
use Broadway\CommandHandling\SimpleCommandHandler;

class JobCommandHandler extends SimpleCommandHandler
{
    public function __construct(private readonly JobRepository $jobRepository)
    {
    }

    public function handleCreateJobCommand(CreateJobCommand $command): void
    {
        $job = Job::newJob($command->getJobId(), $command->getUrl());
        $this->jobRepository->save($job);
    }

    public function handleCreateJobToProcessWebPageContentCommand(CreateJobToProcessWebPageContentCommand $command): void
    {
        $job = Job::newJobToProcessPage($command->getJobId(), $command->getUrl(), $command->getWebPageContent());
        $this->jobRepository->save($job);
    }

    public function handleSetWebPageContentCommand(SetWebPageContentCommand $command): void
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($command->getJobId());
        $job->setWebPageContent($command->getContent());

        $this->jobRepository->save($job);
    }

    public function handleMarkJobAsFailedCommand(MarkJobAsFailedCommand $command): void
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($command->getJobId());
        $job->markAsFailed($command->getReason());

        $this->jobRepository->save($job);
    }

    public function handleSetPushToKindleUrlCommand(SetPushToKindleUrlCommand $command): void
    {
        /** @var Job $job */
        $job = $this->jobRepository->load($command->getJobId());
        $job->setPushToKindleUrl($command->getUrl());

        $this->jobRepository->save($job);
    }
}
