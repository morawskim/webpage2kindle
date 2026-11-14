<?php

namespace App\Infrastructure\Symfony\Event;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransport;
use Symfony\Component\Messenger\Transport\TransportInterface;

class CloseRabbitMqConnectionSubscriber implements EventSubscriberInterface
{
    /** @var list<TransportInterface> */
    private readonly array $transports;

    public function __construct(
        #[Autowire(service: 'messenger.transport.new_job', lazy: true)]
        TransportInterface $newJobTransport,
        #[Autowire(service: 'messenger.transport.fetch_page_content', lazy: true)]
        TransportInterface $fetchPageContentTransport,
        #[Autowire(service: 'messenger.transport.push_to_kindle_url', lazy: true)]
        TransportInterface $pushToKindleUrlTransport,
    ) {
        $this->transports = [$newJobTransport, $fetchPageContentTransport, $pushToKindleUrlTransport];
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::TERMINATE => ['closeRabbitMqConnection']
        ];
    }

    public function closeRabbitMqConnection(): void
    {
        if ('frankenphp' !== php_sapi_name()) {
            return;
        }

        foreach ($this->transports as $transport) {
            if ($transport instanceof AmqpTransport) {
                $transport->close();
            }
        }
    }
}
