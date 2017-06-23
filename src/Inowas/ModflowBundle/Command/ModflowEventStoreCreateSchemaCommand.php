<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Command;

use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\UserId;
use Prooph\EventStore\Adapter\Doctrine\Schema\EventStoreSchema;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ModflowEventStoreCreateSchemaCommand extends ContainerAwareCommand
{

    /** @var  UserId */
    protected $ownerId;

    protected function configure(): void
    {
        // Name and description for app/console command
        $this
            ->setName('inowas:es:schema:create')
            ->setDescription('Creates the database Schema for the eventStore')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): bool
    {
        $this->createEventStreamTableIfNotExists('event_stream');
        return true;
    }

    protected function createEventStreamTableIfNotExists($tableName): void
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
}
