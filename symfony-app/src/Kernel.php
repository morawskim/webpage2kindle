<?php

namespace App;

use App\Infrastructure\Symfony\DependencyInjection\RabbitMqRequestIdProducerPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new RabbitMqRequestIdProducerPass());
    }
}
