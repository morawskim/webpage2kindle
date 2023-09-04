<?php

namespace App\Infrastructure\Symfony\Serializer;

use App\Service\Encrypter;
use Symfony\Component\Serializer\SerializerInterface;

class EncrypterSerializerDecorator implements SerializerInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly Encrypter $encrypter,
    ) {
    }

    public function serialize(mixed $data, string $format, array $context = []): string
    {
        $serializedString = $this->serializer->serialize($data, $format, $context);

        return $this->encrypter->encryptString($serializedString);
    }

    public function deserialize(mixed $data, string $type, string $format, array $context = []): mixed
    {
        if (is_string($data)) {
            $decryptedString = $this->encrypter->decryptString($data);

            return $this->serializer->deserialize($decryptedString, $type, $format, $context);
        }

        return $this->serializer->deserialize($data, $type, $format, $context);
    }
}
