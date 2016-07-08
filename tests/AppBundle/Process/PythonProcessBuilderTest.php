<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\PythonProcess;
use AppBundle\Process\PythonProcessConfiguration;

class PythonProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiationWithDefaultConfiguration(){
        $pythonProcess = new PythonProcess(new PythonProcessConfiguration());
        $this->assertInstanceOf('AppBundle\Process\PythonProcess', $pythonProcess);
    }

    public function testGetProcessReturnsProcess(){
        $pythonProcess = new PythonProcess(new PythonProcessConfiguration());
        $this->assertInstanceOf('Symfony\Component\Process\Process', $pythonProcess->getProcess());
    }

    public function testProcessPrefix(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $pythonProcess = new PythonProcess($configuration);
        $this->assertStringStartsWith('\'pillow', $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessIgnoreWarningsTrueFlag(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(true);
        $pythonProcess = new PythonProcess($configuration);
        $this->assertStringStartsWith("'pillow' '-W' 'ignore'", $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessScriptNameAndIgnoreWarningsTrueFlag(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(true);
        $configuration->setScriptName('myCustomScript.py');
        $pythonProcess = new PythonProcess($configuration);
        $this->assertStringStartsWith("'pillow' '-W' 'ignore' 'myCustomScript.py'", $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessScriptNameAndIgnoreWarningsFalseFlag(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(false);
        $configuration->setScriptName('myCustomScript.py');
        $pythonProcess = new PythonProcess($configuration);
        $this->assertStringStartsWith("'pillow' 'myCustomScript.py'", $pythonProcess->getProcess()->getCommandLine());
    }

    public function testProcessScriptAndArguments(){
        $configuration = new PythonProcessConfiguration();
        $configuration->setPrefix('pillow');
        $configuration->setIgnoreWarnings(false);
        $configuration->setScriptName('myCustomScript.py');
        $configuration->setArguments(array('a', 'b', 'c'));
        $pythonProcess = new PythonProcess($configuration);
        $this->assertStringStartsWith("'pillow' 'myCustomScript.py' 'a' 'b' 'c'", $pythonProcess->getProcess()->getCommandLine());
    }

}