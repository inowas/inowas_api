<?php

namespace Tests\AppBundle\Process;

use AppBundle\Process\PythonProcessFactory;
use AppBundle\Process\ProcessWithInputOutputFileInterface;

class InterpolationProcessTest extends \PHPUnit_Framework_TestCase
{

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $configurationMock;

    public function setUp()
    {
        $this->configurationMock = $this->getMockBuilder('AppBundle\Process\Interpolation\InterpolationProcessConfiguration')
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testInstantiate()
    {
        $this->configurationMock->method('getArguments')->willReturn(array());
        $process = PythonProcessFactory::create($this->configurationMock);


        /** @var ProcessWithInputOutputFileInterface $configurationMock */
        $this->assertInstanceOf('AppBundle\Process\PythonProcess', $process);
    }
}
