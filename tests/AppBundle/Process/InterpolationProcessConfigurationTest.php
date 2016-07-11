<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\InterpolationProcessConfiguration;


class InterpolationProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $configurationFileCreator;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $inputFile;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $outputFile;

    public function setUp()
    {
        $this->inputFile = $this->getMockBuilder('AppBundle\Process\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $this->inputFile->method('getFileName')->willReturn('../inputFile.in');

        $this->outputFile = $this->getMockBuilder('AppBundle\Process\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $this->outputFile->method('getFileName')->willReturn('../outputFile.out');

        $this->configurationFileCreator = $this->getMockBuilder('AppBundle\Service\ConfigurationFileCreatorFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $this->configurationFileCreator->method('getInputFile')->willReturn($this->inputFile);
        $this->configurationFileCreator->method('getOutputFile')->willReturn($this->outputFile);
    }

    public function testInstantiate()
    {
        $interpolationConfiguration = new InterpolationProcessConfiguration($this->configurationFileCreator);
        $this->assertInstanceOf('AppBundle\Process\InterpolationProcessConfiguration', $interpolationConfiguration);
        $this->assertTrue($interpolationConfiguration->getIgnoreWarnings());
        $this->assertEquals('interpolationCalculation.py', $interpolationConfiguration->getScriptName());
    }

    public function testGetter()
    {
        $interpolationConfiguration = new InterpolationProcessConfiguration($this->configurationFileCreator);
        $this->assertEquals('../inputFile.in', $interpolationConfiguration->getInputFile());
        $this->assertEquals('../outputFile.out', $interpolationConfiguration->getOutputFile());
    }

    public function testGetArguments()
    {
        $interpolationConfiguration = new InterpolationProcessConfiguration($this->configurationFileCreator);
        $expectedArguments = array(
            "../inputFile.in",
            "../outputFile.out"
        );
        $this->assertEquals($expectedArguments, $interpolationConfiguration->getArguments());
    }
}
