<?php

namespace App\Service;

use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidFactoryInterface;

class RequestIdGenerator implements \Chrisguitarguy\RequestId\RequestIdGenerator
{
    private $factory;

    public function __construct(?UuidFactoryInterface $factory=null)
    {
        $this->factory = $factory ?: new UuidFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function generate() : string
    {
        return (string) $this->factory->uuid4();
    }
}