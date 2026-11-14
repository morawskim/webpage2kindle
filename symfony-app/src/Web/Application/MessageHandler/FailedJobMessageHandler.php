<?php

namespace App\Web\Application\MessageHandler;

use App\Web\Application\Message\CreatePushToKindleUrlMessage;
use App\Web\Application\Message\FetchPageContentMessage;
use App\Web\Application\Message\NewJobMessage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Handles messages dead-lettered to the "failed-jobs" exchange.
 */
#[AsMessageHandler(fromTransport: 'failed', handles: NewJobMessage::class)]
#[AsMessageHandler(fromTransport: 'failed', handles: FetchPageContentMessage::class)]
#[AsMessageHandler(fromTransport: 'failed', handles: CreatePushToKindleUrlMessage::class)]
class FailedJobMessageHandler
{
    public function __invoke(object $message): void
    {
        // todo mmo analyse job?
    }
}
