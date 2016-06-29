<?php

namespace Tests\AppBundle\Service;

use AppBundle\Service\PythonProcess;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PythonProcessTest extends WebTestCase
{
    /** @var PythonProcess $pythonProcess */
    protected $pythonProcess;

    public function setUp()
    {
        self::bootKernel();

        $this->pythonProcess = static::$kernel->getContainer()
            ->get('inowas.python_process')
        ;
    }

    public function testPythonProcessReturnsProcess(){
        $this->assertInstanceOf('AppBundle\Service\PythonProcess', $this->pythonProcess);
    }

    public function testPythonProcessPreConfiguredPrefixIsSet(){
        $client = static::createClient();
        $prefix =  $client->getKernel()->getContainer()->getParameter('inowas.python_process.prefix');
        $this->assertEquals($prefix, $this->pythonProcess->getPrefix());
    }

    public function testGetAndSetArguments(){
        $arguments = array('Argument1', 'Argument2');
        $this->pythonProcess->setArguments($arguments);
        $this->assertEquals($arguments, $this->pythonProcess->getArguments());
    }

    public function testSetGetPrefix()
    {
        $prefix = "foobar";
        $this->pythonProcess->setPrefix($prefix);
        $this->assertEquals($prefix, $this->pythonProcess->getPrefix());
    }

    public function testSetGetWorkingDirectory()
    {
        $workingDirectory = '/tmp/workingDirectory';
        $this->pythonProcess->setWorkingDirectory($workingDirectory);
        $this->assertEquals($workingDirectory, $this->pythonProcess->getWorkingDirectory());
        $this->assertEquals($workingDirectory, $this->pythonProcess->getProcess()->getWorkingDirectory());
    }

    public function testGetCorrectCommandLineWithPrefixAndArgument()
    {
        $prefix = 'payTon';
        $arguments = array('Argument1', 'Argument2');
        $this->pythonProcess
            ->setPrefix($prefix)
            ->setArguments($arguments);
        $this->assertEquals('\'payTon\' \'Argument1\' \'Argument2\'', $this->pythonProcess->getProcess()->getCommandLine());
    }

    public function testIfPython2_7IsConnected(){
        $process = $this->pythonProcess
            ->setArguments(array('--version'))
            ->getProcess();

        $process->run();
        $this->assertContains('Python 2.7', $process->getErrorOutput());
    }
}