<?php

namespace App\Infrastructure\RabbitMq;

use Countable;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use OutOfRangeException;

class InMemoryProducer implements ProducerInterface, Countable
{
    /** @var array<mixed>  */
    private array $data = [];

    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        $this->data[] = [$msgBody, $routingKey, $additionalProperties];
    }

    public function count(): int
    {
        return count($this->data);
    }

    /**
     * @return array<mixed>
     */
    public function getData(int $index): array
    {
        if (!isset($this->data[$index])) {
            throw new OutOfRangeException(sprintf('The index "%d" does not exist in array', $index));
        }

        return $this->data[$index];
    }
}
