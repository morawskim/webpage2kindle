<?php

namespace App\Web\Infrastructure\Symfony\EventSubscriber;

use App\Job\PrometheusHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class PerformanceSubscriber implements EventSubscriberInterface
{
    public function __construct(private readonly PrometheusHelper $prometheusHelper)
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

        $data['route'] = $event->getRequest()->get('_route') ?? $event->getRequest()->getPathInfo();
        $data['method'] = $event->getRequest()->getRealMethod();
        $data['status_code'] = $event->getResponse()->getStatusCode();

        // prevent storing a lot of unique entries in prometheus when user try to open not existing page
        if (404 === $data['status_code'] && null === $event->getRequest()->get('_route')) {
            $data['route'] = 'unknown-route';
        }

        $duration = ceil((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000);
        $memoryPeak = memory_get_peak_usage(true);

        $this->prometheusHelper->publishRequestMetrics($data, $duration, $memoryPeak);
    }
}
