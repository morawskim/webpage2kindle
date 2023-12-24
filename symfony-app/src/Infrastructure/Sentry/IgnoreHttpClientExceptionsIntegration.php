<?php

namespace App\Infrastructure\Sentry;

use Sentry\Event;
use Sentry\EventHint;
use Sentry\Integration\IntegrationInterface;
use Sentry\SentrySdk;
use Sentry\State\Scope;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class IgnoreHttpClientExceptionsIntegration implements IntegrationInterface
{
    public function setupOnce(): void
    {
        Scope::addGlobalEventProcessor(static function (Event $event, EventHint$hint): ?Event {
            $integration = SentrySdk::getCurrentHub()->getIntegration(self::class);

            if (null !== $integration && $integration->shouldDropEvent($event, $hint)) {
                return null;
            }

            return $event;
        });
    }

    private function shouldDropEvent(Event $event, EventHint $hint): bool
    {
        $exception = $hint->exception;

        if (null === $exception) {
            return false;
        }

        if (!$exception instanceof HttpExceptionInterface) {
            return false;
        }

        return $exception->getStatusCode() >= 400 && $exception->getStatusCode() < 500;
    }
}
