<?php

namespace App\Web\Infrastructure\Symfony\EventSubscriber;

use App\Job\PrometheusHelper;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class PerformanceSubscriber implements EventSubscriberInterface
{
    private array $data = [];

    public function __construct(private readonly PrometheusHelper $prometheusHelper)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TerminateEvent::class => ['onKernelTerminate', -1024],
            RequestEvent::class => ['onKernelRequest', -1024],
            ExceptionEvent::class => ['onKernelException', 0],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->data['route'] = $event->getRequest()->get('_route') ?? 'unknown-route';
        $this->data['method'] = $event->getRequest()->getRealMethod();
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->data['route'] = $event->getRequest()->get('_route') ?? 'unknown-route';
        $this->data['method'] = $event->getRequest()->getRealMethod();
    }

    public function onKernelTerminate(TerminateEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $data = $this->data;
        $data['status_code'] = $event->getResponse()->getStatusCode();
        $duration = ceil((microtime(true) - $_SERVER['REQUEST_TIME_FLOAT']) * 1000);
        $memoryPeak = memory_get_peak_usage(true);

        $this->prometheusHelper->publishRequestMetrics($data, $duration, $memoryPeak);
    }
}
