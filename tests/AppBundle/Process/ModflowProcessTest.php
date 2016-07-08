<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\ModflowProcessBuilder;
use AppBundle\Process\ModflowProcessConfiguration;

class ModflowProcessTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $configurationMock = $this->getMockBuilder('AppBundle\Process\ModflowProcessConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock->method('getArguments')->willReturn(array());
        $configurationMock->method('getIgnoreWarnings')->willReturn(true);
        $configurationMock->method('setIgnoreWarnings')->willReturn($configurationMock);

        /** @var ModflowProcessConfiguration $configurationMock */
        $this->assertInstanceOf('AppBundle\Process\ModflowProcessBuilder', new ModflowProcessBuilder($configurationMock));
    }

    public function testGetProcessReturnsInstanceOfProcess(){
        $configurationMock = $this->getMockBuilder('AppBundle\Process\ModflowProcessConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock->method('getArguments')->willReturn(array());
        $configurationMock->method('getIgnoreWarnings')->willReturn(true);

        /** @var ModflowProcessConfiguration $configurationMock */
        $modflowProcess = new ModflowProcessBuilder($configurationMock);
        $this->assertInstanceOf('Symfony\Component\Process\Process', $modflowProcess->getProcess());
    }

    public function testProcessCommandline(){

        $configurationMock = $this->getMockBuilder('AppBundle\Process\ModflowProcessConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $configurationMock->method('getPrefix')->willReturn('python');
        $configurationMock->method('getIgnoreWarnings')->willReturn(true);
        $configurationMock->method('getArguments')->willReturn(array(
            'http://localhost/',
            'mf2005',
            'dataDirectory/id',
            '../inputFile.in'
        ));
        $configurationMock->method('getScriptName')->willReturn('modflowCalculation.py');

        /** @var ModflowProcessConfiguration $configurationMock */
        $modflowProcess = new ModflowProcessBuilder($configurationMock);
        $this->assertEquals("'python' '-W' 'ignore' 'modflowCalculation.py' 'http://localhost/' 'mf2005' 'dataDirectory/id' '../inputFile.in'", $modflowProcess->getProcess()->getCommandLine());
    }
}
