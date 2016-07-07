<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\ModflowProcess;
use AppBundle\Process\ModflowProcessConfiguration;

class ModflowProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Process\ModflowProcess', new ModflowProcess(new ModflowProcessConfiguration()));
    }
    
    public function testGetProcessReturnsInstanceOfProcess(){
        $modflowProcess = new ModflowProcess(new ModflowProcessConfiguration());
        $this->assertInstanceOf('Symfony\Component\Process\Process', $modflowProcess->getProcess());
    }

    public function testProcessCommandline(){
        $modflowConfiguration = new ModflowProcessConfiguration();
        $modflowConfiguration->setIgnoreWarnings(true);
        $modflowConfiguration->setDataDirectory('dataDirectory/id');
        $modflowConfiguration->setInputFile('../inputFile.in');
        $modflowProcess = new ModflowProcess($modflowConfiguration);
        $this->assertEquals("'python' '-W' 'ignore' 'modflowCalculation.py' 'http://localhost/' 'mf2005' 'dataDirectory/id' '../inputFile.in'", $modflowProcess->getProcess()->getCommandLine());
    }

}
