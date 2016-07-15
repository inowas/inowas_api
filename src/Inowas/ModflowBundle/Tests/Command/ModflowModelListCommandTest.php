<?php

namespace Inowas\ModflowBundle\Tests\Command;

use Inowas\ModflowBundle\Command\ModflowModelListCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ModflowModelCommandTest extends KernelTestCase
{
    public function testExecuteCommandShowsWelcomeMessage()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new ModflowModelListCommand());
        $command = $application->find('inowas:model:list');
        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command' => $command->getName()
        ));

        $this->assertContains('Show all Modflow-Models with ID.', $commandTester->getDisplay());
    }
}
