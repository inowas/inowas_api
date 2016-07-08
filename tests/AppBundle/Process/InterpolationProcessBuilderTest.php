<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\InterpolationProcessBuilder;
use AppBundle\Process\InterpolationProcessConfigurationInterface;

class InterpolationProcessBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $configurationMock;

    public function setUp()
    {
        $this->configurationMock = $this->getMockBuilder('AppBundle\Process\InterpolationProcessConfiguration')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstantiate()
    {
        $interpolationProcessBuilder = new InterpolationProcessBuilder($this->configurationMock);

        /** @var InterpolationProcessConfigurationInterface $configurationMock */
        $this->assertInstanceOf('AppBundle\Process\InterpolationProcessBuilder', $interpolationProcessBuilder);
    }

    public function testGetProcessReturnsInstanceOfProcess()
    {
        $this->configurationMock->method('getArguments')->willReturn(array());
        $interpolationProcessBuilder = new InterpolationProcessBuilder($this->configurationMock);
        $this->assertInstanceOf('Symfony\Component\Process\Process', $interpolationProcessBuilder->getProcess());
    }

    public function testProcessCommandline()
    {
        $this->configurationMock->method('getPrefix')->willReturn('python');
        $this->configurationMock->method('getIgnoreWarnings')->willReturn(true);
        $this->configurationMock->method('getArguments')->willReturn(array(
            '../inputFile.in',
            '../outputFile.out'
        ));
        $this->configurationMock->method('getScriptName')->willReturn('interpolationCalculation.py');
        $interpolationProcessBuilder = new InterpolationProcessBuilder($this->configurationMock);
        $this->assertEquals("'python' '-W' 'ignore' 'interpolationCalculation.py' '../inputFile.in' '../outputFile.out'", $interpolationProcessBuilder->getProcess()->getCommandLine());
    }
}
