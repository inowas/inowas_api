<?php

namespace Tests\Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Model\ModflowCalculationProcessConfiguration;

class ModflowCalculationProcessConfigurationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowCalculationProcessConfiguration */
    protected $modflowCalculationProcessConfiguration;

    public function setUp(){

        $inputFile = $this->getMockBuilder('Inowas\PythonProcessBundle\Model\ProcessFile')
            ->disableOriginalConstructor()
            ->getMock();

        $inputFile->method('getFileName')->willReturn('inputFilename');

        $this->modflowCalculationProcessConfiguration = new ModflowCalculationProcessConfiguration(
            $inputFile, 'workspace', 'executable', 'baseUrl'
        );
    }

    public function testInstantiate(){
        $this->assertInstanceOf('Inowas\ModflowBundle\Model\ModflowCalculationProcessConfiguration', $this->modflowCalculationProcessConfiguration);
        $this->assertContains('inputFilename', $this->modflowCalculationProcessConfiguration->getArguments());
        $this->assertContains('workspace', $this->modflowCalculationProcessConfiguration->getArguments());
        $this->assertContains('executable', $this->modflowCalculationProcessConfiguration->getArguments());
        $this->assertContains('baseUrl', $this->modflowCalculationProcessConfiguration->getArguments());
    }
}
