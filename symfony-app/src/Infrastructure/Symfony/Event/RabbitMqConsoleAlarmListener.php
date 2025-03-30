<?php

namespace App\Infrastructure\Symfony\Event;

use Symfony\Component\Console\Event\ConsoleAlarmEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class RabbitMqConsoleAlarmListener
{
    #[AsEventListener(event: ConsoleAlarmEvent::class)]
    public function onConsoleAlarm(ConsoleAlarmEvent $event): void
    {
        if ('rabbitmq:multiple-consumer' === $event->getCommand()->getName()) {
            $event->abortExit();
        }
    }
}
