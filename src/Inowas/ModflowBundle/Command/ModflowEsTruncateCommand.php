<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Doctrine\DBAL\Schema\Schema;
use Inowas\Modflow\Model\UserId;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowEsTruncateCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:es:truncate')
            ->setDescription('Truncates the event-stream Database');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->truncateEventStreamTable('event_stream');

    }

    private function truncateEventStreamTable($tableName)
    {
        $this->dropEventStreamTable($tableName);
        $this->createEventStreamTableIfNotExists($tableName);
    }

    private function dropEventStreamTable($tableName)
    {
        $connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        if (! in_array($tableName, $connection->getSchemaManager()->listTableNames())){
            return;
        }

        $schema = new Schema();
        $schema->createTable($tableName);
        $queries = $schema->toDropSql($connection->getDatabasePlatform());

        foreach ($queries as $query){
            $connection->exec($query);
        }
    }

    private function createEventStreamTableIfNotExists($tableName): void
    {
        $connection = $this->getContainer()->get('doctrine.dbal.default_connection');

        if (in_array($tableName, $connection->getSchemaManager()->listTableNames())){
            return;
        }

        $schema = new Schema();
        if (class_exists('Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema')) {
            EventStoreSchema::createSingleStream($schema, $tableName, true);
        }

        $queries = $schema->toSql($connection->getDatabasePlatform());

        foreach ($queries as $query){
            $connection->exec($query);
        }
    }
}
