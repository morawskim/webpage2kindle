<?php

namespace App\Web\Infrastructure\Symfony\Serializer;

use App\Job\Domain\JobId;
use Symfony\Component\PropertyInfo\Type;
use Symfony\Component\Serializer\Exception\InvalidArgumentException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class JobIdNormalizer implements NormalizerInterface, DenormalizerInterface
{
    /**
     * @phpstan-ignore-next-line
     */
    public function normalize(mixed $object, string $format = null, array $context = []): float|array|\ArrayObject|bool|int|string|null
    {
        if (!$object instanceof JobId) {
            throw new InvalidArgumentException(sprintf('The object must be an instance of "%s".', JobId::class));
        }

        return (string) $object;
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof JobId;
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): mixed
    {
        if ('' === $data || null === $data) {
            throw NotNormalizableValueException::createForUnexpectedDataType('The data is either an empty string or null, you should pass a string that can be parsed as a JobId.', $data, [Type::BUILTIN_TYPE_STRING], $context['deserialization_path'] ?? null, true);
        }

        return new JobId($data);
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null, array $context = []): bool
    {
        return $type === JobId::class;
    }

    /**
     * @phpstan-ignore-next-line
     */
    public function getSupportedTypes(?string $format): array
    {
        return [
            JobId::class => true,
        ];
    }
}
