<?php

namespace Tests\AppBundle\Command;

use AppBundle\Command\CacheClearCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class CacheClearCommandTest extends KernelTestCase
{

    public function setUp()
    {
    }

    public function testExecute()
    {
        $kernel = $this->createKernel();
        $kernel->boot();

        $application = new Application($kernel);
        $application->add(new CacheClearCommand());
        $command = $application->find('inowas:cache:clear');
        $commandTester = new CommandTester($command);
        
        $geoImgDataFolder = $kernel->getContainer()->getParameter('inowas.geoimage.data_folder');
        $tmpFolder = $kernel->getContainer()->getParameter('inowas.temp_folder');

        $fs = new Filesystem();
        $fs->dumpFile($geoImgDataFolder.'/testFile', "test");
        $fs->dumpFile($tmpFolder.'/testFile', "test");
        $this->assertTrue($fs->exists($geoImgDataFolder.'/testFile'));
        $this->assertTrue($fs->exists($tmpFolder.'/testFile'));

        $commandTester->execute(array('command' => $command->getName()));
        $this->assertContains('Clear Temp-Folder', $commandTester->getDisplay());
        $this->assertContains('Clear Image-Folder', $commandTester->getDisplay());

        $this->assertFalse($fs->exists($geoImgDataFolder.'/testFile'));
        $this->assertFalse($fs->exists($tmpFolder.'/testFile'));
    }

    public function tearDown()
    {
    }

}
