<?php

namespace App\Infrastructure\Symfony\DependencyInjection;

use App\Infrastructure\RabbitMq\ProducerRequestIdStamp;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Chrisguitarguy\RequestId\RequestIdStorage;

class RabbitMqRequestIdProducerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $producers = $container->findTaggedServiceIds('old_sound_rabbit_mq.producer');
        foreach ($producers as $serviceId => $tags) {
            $definition = $container->getDefinition($serviceId);
            if (ProducerRequestIdStamp::class !== $definition->getClass()) {
                continue;
            }

            $definition->addMethodCall('setRequestStorageId', [new Reference(RequestIdStorage::class)]);
        }
    }
}
