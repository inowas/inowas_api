<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\UserId;
use Prooph\EventStore\Stream;
use Prooph\EventStore\StreamName;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Prooph\EventStore\Exception\StreamNotFound;

class ModflowEventStoreTruncateCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:es:truncate')
            ->setDescription('Truncates the event-stream Database and cleans the local modflow-data folder');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $config = $this->getContainer()->getParameter('prooph_event_store_repositories');

        foreach ($config as $repo) {
            $this->getContainer()->get('prooph_event_store')->delete(new  StreamName($repo['stream_name']));
            $this->getContainer()->get('prooph_event_store')->create(
                new Stream(new  StreamName($repo['stream_name']), new \ArrayIterator())
            );
        }

        $this->cleanDataFolder();
    }

    private function truncateEventStreamTable($tableName): void
    {
        $this->dropEventStreamTable($tableName);
        $this->createEventStreamTableIfNotExists($tableName);
    }

    private function dropEventStreamTable($tableName): void
    {
        $connection = $this->getContainer()->get('doctrine.dbal.default_connection');
        if (! in_array($tableName, $connection->getSchemaManager()->listTableNames(), true)){
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

        if (in_array($tableName, $connection->getSchemaManager()->listTableNames(), true)){
            return;
        }

        $schema = new Schema();
        if (class_exists(EventStoreSchema::class)) {
            EventStoreSchema::createSingleStream($schema, $tableName, true);
        }

        $queries = $schema->toSql($connection->getDatabasePlatform());

        foreach ($queries as $query){
            $connection->exec($query);
        }
    }

    private function cleanDataFolder(): void
    {
        /*
        $dataFolder = $this->getContainer()->getParameter('inowas.modflow.data_folder');
        $fs = new Filesystem();
        $fs->remove($dataFolder);
        $fs->mkdir($dataFolder);
        $fs->touch($dataFolder.'/.gitkeep');
        */
    }
}
