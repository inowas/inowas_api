<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Tests\Functional;

use Doctrine\DBAL\Connection;
use Inowas\ModflowBundle\Command\ModflowEventStoreTruncateCommand;
use Inowas\ModflowBundle\Command\ModflowProjectionCommand;
use Prooph\EventStore\EventStore;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EventSourcingBaseTest extends KernelTestCase
{

    /** @var  ContainerInterface */
    protected $container;

    /** @var  CommandBus */
    protected $commandBus;

    /** @var  EventBus */
    protected $eventBus;

    /** @var  EventStore */
    protected $eventStore;

    /** @var  Connection */
    protected $connection;


    public function setUp(): void
    {
        self::bootKernel();

        $application = new Application(static::$kernel);
        $application->add(new ModflowEventStoreTruncateCommand());
        $application->add(new ModflowProjectionCommand());

        $command = $application->find('inowas:es:truncate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $command = $application->find('inowas:projections:reset');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->container = static::$kernel->getContainer();
        $this->connection = static::$kernel->getContainer()->get('doctrine.dbal.default_connection');
        $this->commandBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->eventBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_event_bus');
        $this->eventStore = static::$kernel->getContainer()->get('prooph_event_store.modflow_model_store');
    }
}
