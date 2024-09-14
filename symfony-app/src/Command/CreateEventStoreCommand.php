<?php

/*
 * Copied from broadway/broadway-demo package.
 *
 * (c) 2020 Broadway project
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Broadway\EventStore\Dbal\DBALEventStore;
use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEventStoreCommand extends Command
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @var DBALEventStore
     */
    private $eventStore;

    public function __construct(Connection $connection, DBALEventStore $eventStore)
    {
        $this->connection = $connection;
        $this->eventStore = $eventStore;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('broadway:event-store:create')
            ->setDescription('Creates the event store schema')
            ->setHelp(
                <<<EOT
The <info>%command.name%</info> command creates the schema in the default
connections database:
<info>php app/console %command.name%</info>
EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $schemaManager = $this->connection->createSchemaManager();

        if ($table = $this->eventStore->configureSchema($schemaManager->introspectSchema())) {
            $schemaManager->createTable($table);
            $output->writeln('<info>Created Broadway event store schema</info>');
        } else {
            $output->writeln('<info>Broadway event store schema already exists</info>');
        }

        return Command::SUCCESS;
    }
}
