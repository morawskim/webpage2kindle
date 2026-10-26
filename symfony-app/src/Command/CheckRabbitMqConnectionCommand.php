<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Messenger\Bridge\Amqp\Transport\AmqpTransport;
use Symfony\Component\Messenger\Exception\TransportException;
use Symfony\Component\Messenger\Transport\InMemory\InMemoryTransport;

class CheckRabbitMqConnectionCommand extends Command
{
    public function __construct(
        #[Autowire(service: 'messenger.transport.fetch_page_content')]
        private readonly AmqpTransport|InMemoryTransport $transport,

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
            $this->transport->getMessageCount();
        } catch (TransportException $e) {
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
