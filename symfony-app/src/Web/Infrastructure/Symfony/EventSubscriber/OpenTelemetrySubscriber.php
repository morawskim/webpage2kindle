<?php

namespace App\Web\Infrastructure\Symfony\EventSubscriber;

use OpenTelemetry\API\Globals;
use OpenTelemetry\SDK\Trace\TracerProviderInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class OpenTelemetrySubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => ['onKernelTerminate', 1024],
        ];
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $traceProvider = Globals::tracerProvider();
        if (!$traceProvider instanceof TracerProviderInterface) {
            return;
        }

        $result = $traceProvider->forceFlush();
        if (!$result) {
            $this->logger->warning('Flush OpenTelemetry traces failed');
        }
    }
}
