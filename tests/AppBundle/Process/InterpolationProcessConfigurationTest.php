<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\InterpolationProcessConfiguration;

class InterpolationProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $inputFileMock;
    
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $outputFileMock;

    public function setUp()
    {
        $this->inputFileMock = $this->getMockBuilder('AppBundle\Process\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();

        $this->outputFileMock = $this->getMockBuilder('AppBundle\Process\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstantiate(){
        $interpolationConfiguration = new InterpolationProcessConfiguration($this->inputFileMock, $this->outputFileMock);
        $this->assertInstanceOf('AppBundle\Process\InterpolationProcessConfiguration', $interpolationConfiguration);
        $this->assertTrue($interpolationConfiguration->getIgnoreWarnings());
        $this->assertEquals('interpolationCalculation.py', $interpolationConfiguration->getScriptName());
    }

    public function testSetterGetter(){

        $this->inputFileMock->method('getFileName')->willReturn('inputTestFile');
        $this->outputFileMock->method('getFileName')->willReturn('outputTestFile');
        
        $interpolationConfiguration = new InterpolationProcessConfiguration($this->inputFileMock, $this->outputFileMock);

        $interpolationConfiguration->setInputFile($this->inputFileMock);
        $this->assertEquals('inputTestFile', $interpolationConfiguration->getInputFile());

        $interpolationConfiguration->setOutputFile($this->outputFileMock);
        $this->assertEquals('outputTestFile', $interpolationConfiguration->getOutputFile());

        $directory = "../directory";
        $interpolationConfiguration->setDataDirectory($directory);
        $this->assertEquals($directory, $interpolationConfiguration->getDataDirectory());

        $expectedArguments = array(
            'inputTestFile',
            'outputTestFile'
        );
        $this->assertEquals($expectedArguments, $interpolationConfiguration->getArguments());
    }

}
