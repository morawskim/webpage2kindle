<?php

namespace App\Web\Application\MessageHandler;

use App\Job\Exception\CannotGetPageContentException;
use App\Job\PushToKindlePipelineService;
use App\Web\Application\Message\FetchPageContentMessage;
use App\Web\Domain\Contract\PushToKindleProducerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'fetch_page_content')]
class FetchPageContentMessageHandler
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
        private readonly PushToKindleProducerInterface $pushToKindleProducer,
    ) {
    }

    public function __invoke(FetchPageContentMessage $message): void
    {
        try {
            $this->pushToKindlePipeline->fetchPageContentForJob($message->jobId);
            $this->pushToKindleProducer->publishPushToKindle($message->jobId);
        } catch (CannotGetPageContentException $e) {
        }
    }
}
