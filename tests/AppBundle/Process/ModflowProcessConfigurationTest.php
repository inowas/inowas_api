<?php

namespace Tests\AppBundle\Process;

use Inowas\PythonProcessBundle\Model\ModflowProcessConfiguration;
use Inowas\PythonProcessBundle\Model\ProcessFile;

class ModflowProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        /** @var ProcessFile $processFile */
        $processFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();

        $modflowConfiguration = new ModflowProcessConfiguration($processFile);

        $this->assertInstanceOf('Inowas\PythonProcessBundle\Model\ModflowProcessConfiguration', $modflowConfiguration);
        $this->assertTrue($modflowConfiguration->getIgnoreWarnings());
        $this->assertEquals('modflowCalculation.py', $modflowConfiguration->getScriptName());
        $this->assertEquals('http://localhost/', $modflowConfiguration->getAPIBaseUrl());
        $this->assertEquals('mf2005', $modflowConfiguration->getExecutable());
    }
    
    public function testSetterGetter(){

        $processInputFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $processInputFile->method('getFileName')->willReturn('inputTestFile');

        $processOutputFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $processOutputFile->method('getFileName')->willReturn('outputTestFile');

        /** @var \Inowas\PythonProcessBundle\Model\ProcessFile $processInputFile */
        $modflowConfiguration = new ModflowProcessConfiguration($processInputFile);

        $apiBaseUrl = "http://api.base.url";
        $modflowConfiguration->setAPIBaseUrl($apiBaseUrl);
        $this->assertEquals($apiBaseUrl, $modflowConfiguration->getAPIBaseUrl());

        $modflowConfiguration->setInputFile($processInputFile);
        $this->assertEquals('inputTestFile', $modflowConfiguration->getInputFile());

        /** @var \Inowas\PythonProcessBundle\Model\ProcessFile $processOutputFile */
        $modflowConfiguration->setOutputFile($processOutputFile);
        $this->assertEquals('outputTestFile', $modflowConfiguration->getOutputFile());

        $directory = "../directory";
        $modflowConfiguration->setDataDirectory($directory);
        $this->assertEquals($directory, $modflowConfiguration->getDataDirectory());

        $executable = "mf2005";
        $modflowConfiguration->setExecutable($executable);
        $this->assertEquals($executable, $modflowConfiguration->getExecutable());

        $expectedArguments = array(
            'http://api.base.url',
            'mf2005',
            '../directory',
            'inputTestFile'
        );
        $this->assertEquals($expectedArguments, $modflowConfiguration->getArguments());
    }

    public function testSetUnknownExecutableThrowsException(){
        $processInputFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $processInputFile->method('getFileName')->willReturn('inputTestFile');

        /** @var \Inowas\PythonProcessBundle\Model\ProcessFile $processInputFile */
        $modflowConfiguration = new ModflowProcessConfiguration($processInputFile);

        $this->setExpectedException('AppBundle\Exception\InvalidArgumentException');
        $modflowConfiguration->setExecutable('unknownExecutable');
    }
}
