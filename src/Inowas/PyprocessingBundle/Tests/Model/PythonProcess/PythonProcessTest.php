<?php

namespace Inowas\PyprocessingBundle\Tests\Model\PythonProcess;

use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcess;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

class PythonProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiationWithDefaultConfiguration(){
        $pythonProcess = new PythonProcess(new ProcessBuilder(), new PythonProcessConfiguration());
        $this->assertInstanceOf(PythonProcess::class, $pythonProcess);
    }

    public function testGetProcessReturnsProcess(){
        $pythonProcess = new PythonProcess(new ProcessBuilder(), new PythonProcessConfiguration());
        $this->assertInstanceOf(Process::class, $pythonProcess->getProcess());
    }

    public function testProcessPrefix(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $pythonProcess = new PythonProcess(new ProcessBuilder(), $configuration);
        $this->assertStringStartsWith('\'pillow', $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessIgnoreWarningsTrueFlag(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(true);
        $pythonProcess = new PythonProcess(new ProcessBuilder(), $configuration);
        $this->assertStringStartsWith("'pillow' '-W' 'ignore'", $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessScriptNameAndIgnoreWarningsTrueFlag(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(true);
        $configuration->setScriptName('myCustomScript.py');
        $pythonProcess = new PythonProcess(new ProcessBuilder(), $configuration);
        $this->assertEquals("'pillow' '-W' 'ignore' 'myCustomScript.py'", $pythonProcess->getProcess()->getCommandLine());

    }

    public function testProcessScriptNameAndIgnoreWarningsFalseFlag(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(false);
        $configuration->setScriptName('myCustomScript.py');
        $pythonProcess = new PythonProcess(new ProcessBuilder(), $configuration);
        $this->assertEquals("'pillow' 'myCustomScript.py'", $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessScriptAndArguments(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(false);
        $configuration->setScriptName('myCustomScript.py');
        $configuration->setArguments(array('a', 'b', 'c'));
        $pythonProcess = new PythonProcess(new ProcessBuilder(), $configuration);
        $this->assertEquals("'pillow' 'myCustomScript.py' 'a' 'b' 'c'", $pythonProcess->getProcess()->getCommandLine());
    }
    
    public function testProcessIsRunningMethod(){

        $processMock = $this->getMockBuilder(Process::class)
            ->disableOriginalConstructor()
            ->getMock();

        $processMock->method('isRunning')->willReturn(false);
        $processMock->method('isSuccessful')->willReturn(false);

        $processBuilderMock = $this->getMockBuilder(ProcessBuilder::class)
            ->setConstructorArgs(array())
            ->setMethods(array(
                'setPrefix',
                'setWorkingDirectory',
                'add',
                'getProcess'
            ))
            ->getMock();

        $processBuilderMock->method('getProcess')->willReturn($processMock);

        $configuration = new PythonProcessConfiguration();
        $pythonProcess = new PythonProcess($processBuilderMock, $configuration);
        $this->assertEquals(false, $pythonProcess->isRunning());
        $this->assertEquals(false, $pythonProcess->isSuccessful());
    }
}