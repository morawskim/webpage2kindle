<?php

namespace App\Web\Application\MessageHandler;

use App\Job\PushToKindlePipelineService;
use App\Web\Application\Message\NewJobMessage;
use App\Web\Domain\Contract\FetchPageContentProducerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'new_job')]
class NewJobMessageHandler
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly FetchPageContentProducerInterface $fetchPageContentProducer,
    ) {
    }

    public function __invoke(NewJobMessage $message): void
    {
        $this->pushToKindlePipeline->addNewJob($message->jobId, $message->url);
        $this->fetchPageContentProducer->publishFetchPageContent($message->jobId);
    }
}
