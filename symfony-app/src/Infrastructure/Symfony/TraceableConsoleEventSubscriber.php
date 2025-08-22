<?php

namespace App\Infrastructure\Symfony;

use OpenTelemetry\API\Globals;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use OpenTelemetry\API\Trace\Span;
use OpenTelemetry\API\Trace\StatusCode;
use OpenTelemetry\API\Trace\TracerInterface;
use OpenTelemetry\Context\Context;
use OpenTelemetry\SemConv\TraceAttributes;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleSignalEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;

class TraceableConsoleEventSubscriber implements EventSubscriberInterface
{
    private const SYMFONY_EXIT_CODE_ATTRIBUTE = 'symfony.console.exit_code';
    private const SYMFONY_SIGNAL_ATTRIBUTE = 'symfony.console.signal_code';
    public function __construct(
        private readonly ?LoggerInterface $logger = null,
    ) {
    }
    public static function getSubscribedEvents(): array
    {
        return [
            ConsoleEvents::COMMAND => [
                ['startSpan', 10000],
            ],
            ConsoleEvents::ERROR => [
                ['handleError', -10000],
            ],
            ConsoleEvents::TERMINATE => [
                ['endSpan', -10000],
            ],
            ConsoleEvents::SIGNAL => [
                ['handleSignal', -10000],
            ],
        ];
    }

    public function startSpan(ConsoleCommandEvent $event): void
    {
        $command = $event->getCommand();

        assert($command instanceof Command);

        $tracer = $this->getTracer($command);

        $name = $command->getName();
        $class = get_class($command);

        $spanBuilder = $tracer
            ->spanBuilder($name)
            ->setAttributes([
                TraceAttributes::CODE_FUNCTION_NAME => $class.'::execute',
            ]);

        $parent = Context::getCurrent();

        $span = $spanBuilder->setParent($parent)->startSpan();

        $this->logger?->debug(sprintf('Starting span "%s"', $span->getContext()->getSpanId()));

        Context::storage()->attach($span->storeInContext($parent));

        $this->logger?->debug(sprintf('Activating new scope "%s"', spl_object_id(Context::storage()->scope())));
    }

    public function handleError(ConsoleErrorEvent $event): void
    {
        $span = Span::getCurrent();
        $span->setStatus(StatusCode::STATUS_ERROR);
        $span->recordException($event->getError(), [
            self::SYMFONY_EXIT_CODE_ATTRIBUTE => $event->getExitCode(),
        ]);
    }

    public function endSpan(ConsoleTerminateEvent $event): void
    {
        $scope = Context::storage()->scope();
        if (null === $scope) {
            $this->logger?->debug('No active scope');

            return;
        }
        $this->logger?->debug(sprintf('Detaching scope "%s"', spl_object_id($scope)));
        $scope->detach();

        $span = Span::fromContext($scope->context());
        $span->setAttribute(
            self::SYMFONY_EXIT_CODE_ATTRIBUTE,
            $event->getExitCode()
        );

        $statusCode = match ($event->getExitCode()) {
            Command::SUCCESS => StatusCode::STATUS_OK,
            default => StatusCode::STATUS_ERROR,
        };
        $span->setStatus($statusCode);

        $this->logger?->debug(sprintf('Ending span "%s"', $span->getContext()->getSpanId()));
        $span->end();
    }

    public function handleSignal(ConsoleSignalEvent $event): void
    {
        $span = Span::getCurrent();
        $span->setAttribute(self::SYMFONY_SIGNAL_ATTRIBUTE, $event->getHandlingSignal());
    }

    private function getTracer(Command $command): TracerInterface
    {
        $tracerProvider = Globals::tracerProvider();
        return $tracerProvider->getTracer('symfony');
    }
}
