<?php

namespace AppBundle\Tests\Command;

use AppBundle\Command\InterpolationCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class InterpolationCommandTest extends KernelTestCase
{

    public function setUp()
    {
    }

    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new InterpolationCommand());
        $command = $application->find('inowas:model:interpolate');
        $commandTester = new CommandTester($command);

        $commandTester->execute(array(
            'command' => $command->getName(),
            'id' => 123)
        );

        $this->assertContains('Interpolating Layers', $commandTester->getDisplay());
    }

    public function tearDown()
    {
    }

}
