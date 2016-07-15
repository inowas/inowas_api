<?php

namespace Tests\AppBundle\Process\Modflow;

use AppBundle\Process\Modflow\ModflowResultProcessConfiguration;

class ModflowResultProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowResultProcessConfiguration */
    protected $modflowResultProcessConfiguration;

    public function setUp(){

        $inputFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $inputFile->method('getFileName')->willReturn('inputFilename');

        $outputFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();
        $outputFile->method('getFileName')->willReturn('outputFilename');

        $this->modflowResultProcessConfiguration = new ModflowResultProcessConfiguration(
            $inputFile, $outputFile, 'workspace',  'baseUrl'
        );
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Process\Modflow\ModflowResultProcessConfiguration', $this->modflowResultProcessConfiguration);
        $this->assertContains('inputFilename', $this->modflowResultProcessConfiguration->getArguments());
        $this->assertContains('outputFilename', $this->modflowResultProcessConfiguration->getArguments());
        $this->assertContains('workspace', $this->modflowResultProcessConfiguration->getArguments());
        $this->assertContains('baseUrl', $this->modflowResultProcessConfiguration->getArguments());
    }
}
