<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Modflow;

use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultProcessConfiguration;
use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;

class ModflowResultProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowResultProcessConfiguration */
    protected $modflowResultProcessConfiguration;

    public function setUp(){

        $inputFile = $this->getMockBuilder(ProcessFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inputFile->method('getFileName')->willReturn('inputFilename');

        $outputFile = $this->getMockBuilder(ProcessFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $outputFile->method('getFileName')->willReturn('outputFilename');

        $this->modflowResultProcessConfiguration = new ModflowResultProcessConfiguration(
            $inputFile, $outputFile, 'workspace',  'baseUrl'
        );
    }

    public function testInstantiate(){
        $this->assertInstanceOf(ModflowResultProcessConfiguration::class, $this->modflowResultProcessConfiguration);
        $this->assertContains('inputFilename', $this->modflowResultProcessConfiguration->getArguments());
        $this->assertContains('outputFilename', $this->modflowResultProcessConfiguration->getArguments());
        $this->assertContains('workspace', $this->modflowResultProcessConfiguration->getArguments());
        $this->assertContains('baseUrl', $this->modflowResultProcessConfiguration->getArguments());
    }
}
