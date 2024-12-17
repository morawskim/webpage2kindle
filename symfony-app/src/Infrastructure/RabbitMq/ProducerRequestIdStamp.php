<?php

namespace App\Infrastructure\RabbitMq;

use Chrisguitarguy\RequestId\RequestIdStorage;
use OldSound\RabbitMqBundle\RabbitMq\Producer;

class ProducerRequestIdStamp extends Producer
{
    private RequestIdStorage $requestIdStorage;

    public function setRequestStorageId(RequestIdStorage $requestIdStorage): void
    {
        $this->requestIdStorage = $requestIdStorage;
    }

    public function publish($msgBody, $routingKey = null, $additionalProperties = array(), ?array $headers = null)
    {
        if (empty($headers)) {
            $headers = [];
        }

        $headers['X-RequestId'] = $this->requestIdStorage->getRequestId() ?? 'not-set';

        parent::publish($msgBody, $routingKey, $additionalProperties, $headers);
    }
}
