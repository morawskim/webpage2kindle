<?php

namespace App\Web\Application\MessageHandler;

use App\Job\Exception\CannotCreatePastePadUrlException;
use App\Job\PushToKindlePipelineService;
use App\Web\Application\Message\CreatePushToKindleUrlMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(fromTransport: 'push_to_kindle_url')]
class CreatePushToKindleUrlMessageHandler
{
    public function __construct(
        private readonly PushToKindlePipelineService $pushToKindlePipeline,
    ) {
    }

    public function __invoke(CreatePushToKindleUrlMessage $message): void
    {
        try {
            $this->pushToKindlePipeline->createPushToKindleUrl($message->jobId);
        } catch (CannotCreatePastePadUrlException $e) {
        }
    }
}
