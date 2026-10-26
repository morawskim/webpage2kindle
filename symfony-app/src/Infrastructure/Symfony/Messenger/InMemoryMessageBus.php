<?php

namespace App\Infrastructure\Symfony\Messenger;

use OutOfRangeException;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class InMemoryMessageBus implements MessageBusInterface
{
    /** @var array<object>  */
    private array $data = [];

    public function dispatch(object $message, array $stamps = []): Envelope
    {
        $this->data[] = $message;

        return new Envelope($message, $stamps);
    }

    public function count(): int
    {
        return count($this->data);
    }

    public function getData(int $index): object
    {
        if (!isset($this->data[$index])) {
            throw new OutOfRangeException(sprintf('The index "%d" does not exist in array', $index));
        }

        return $this->data[$index];
    }
}
