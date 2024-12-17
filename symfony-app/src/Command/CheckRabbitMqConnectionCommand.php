<?php

namespace App\Command;

use OldSound\RabbitMqBundle\RabbitMq\Producer;
use PhpAmqpLib\Exception\AMQPIOException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CheckRabbitMqConnectionCommand extends Command
{
    public function __construct(
        #[Autowire(service: 'old_sound_rabbit_mq.fetch_page_content_producer')]
        private readonly Producer $producer,
        ?string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('app:check-connection-to-rabbitmq')
            ->setDescription('Check connection with RabbitMQ')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command check connection to RabbitMQ.
<info>php app/console %command.name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->producer->getChannel()->queue_declare(
                'tmp',
                false,
                false,
                false,
                true
            );
            $this->producer->getChannel()->queue_delete('tmp');
        } catch (AMQPIOException $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
