<?php

namespace App\Infrastructure\Messenger\Serializer;

use Chrisguitarguy\RequestId\RequestIdStorage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Transport\Serialization\SerializerInterface;
use Symfony\Component\Serializer\SerializerInterface as SymfonySerializerInterface;

class EncryptedMessengerSerializer implements SerializerInterface
{
    public function __construct(
        private readonly SymfonySerializerInterface $encrypterSerializer,
        private readonly RequestIdStorage $requestIdStorage,
    ) {
    }

    public function decode(array $encodedEnvelope): Envelope
    {
        if (empty($encodedEnvelope['body'])) {
            throw new \InvalidArgumentException('Encoded envelope should have at least a "body".');
        }

        $headers = $encodedEnvelope['headers'] ?? [];
        $type = $headers['type'] ?? null;

        if (null === $type) {
            throw new \InvalidArgumentException('Encoded envelope does not have a "type" header.');
        }

        $message = $this->encrypterSerializer->deserialize($encodedEnvelope['body'], $type, 'json');

        return new Envelope($message);
    }

    public function encode(Envelope $envelope): array
    {
        $message = $envelope->getMessage();
        $body = $this->encrypterSerializer->serialize($message, 'json');

        $headers = [
            'type' => get_class($message),
            'Content-Type' => 'application/json',
            'X-RequestId' => $this->requestIdStorage->getRequestId() ?? 'not-set',
        ];

        return [
            'body' => $body,
            'headers' => $headers,
        ];
    }
}
