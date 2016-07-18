<?php

namespace InowasPyprocessingBundle\Tests\Model\PythonProcess;

use InowasPyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;

class PythonProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiation(){
        $configuration = new PythonProcessConfiguration();
        $this->assertInstanceOf(PythonProcessConfiguration::class, $configuration);
        $this->assertEquals(array(), $configuration->getArguments());
        $this->assertEquals('../py/pyprocessing', $configuration->getWorkingDirectory());
        $this->assertEquals(true, $configuration->getIgnoreWarnings());
        $this->assertEquals('python', $configuration->getPrefix());
        $this->assertEquals('', $configuration->getScriptName());
    }

    public function testGetterSetter()
    {
        $prefix = 'pillow';
        $arguments = array('a', 'b', 'c');
        $workingDirectory = '../py/pyprop';
        $ignoreWarnings = false;
        $scriptName = 'myCustomScript.py';

        $configuration = new PythonProcessConfiguration();
        $configuration->setArguments($arguments);
        $this->assertEquals($arguments, $configuration->getArguments());

        $configuration->setPrefix($prefix);
        $this->assertEquals($prefix, $configuration->getPrefix());

        $configuration->setWorkingDirectory($workingDirectory);
        $this->assertEquals($workingDirectory, $configuration->getWorkingDirectory());

        $configuration->setIgnoreWarnings($ignoreWarnings);
        $this->assertEquals($ignoreWarnings, $configuration->getIgnoreWarnings());

        $configuration->setScriptName($scriptName);
        $this->assertEquals($scriptName, $configuration->getScriptName());
    }

}
