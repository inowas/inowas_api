<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Modflow;

use Inowas\PyprocessingBundle\Model\Modflow\ModflowCalculationProcessConfiguration;
use Inowas\PyprocessingBundle\Model\PythonProcess\ProcessFile;

class ModflowCalculationProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowCalculationProcessConfiguration */
    protected $modflowCalculationProcessConfiguration;

    public function setUp(){

        $inputFile = $this->getMockBuilder(ProcessFile::class)
            ->disableOriginalConstructor()
            ->getMock();

        $inputFile->method('getFileName')->willReturn('inputFilename');

        $this->modflowCalculationProcessConfiguration = new ModflowCalculationProcessConfiguration(
            $inputFile, 'workspace', 'executable', 'baseUrl'
        );
    }

    public function testInstantiate(){
        $this->assertInstanceOf(ModflowCalculationProcessConfiguration::class, $this->modflowCalculationProcessConfiguration);
        $this->assertContains('inputFilename', $this->modflowCalculationProcessConfiguration->getArguments());
        $this->assertContains('workspace', $this->modflowCalculationProcessConfiguration->getArguments());
        $this->assertContains('executable', $this->modflowCalculationProcessConfiguration->getArguments());
        $this->assertContains('baseUrl', $this->modflowCalculationProcessConfiguration->getArguments());
    }
}
