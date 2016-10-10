<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowKernel;

class ModflowKernelTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateId()
    {
        $modflowKernel = new ModFlowKernel();
        $this->assertInstanceOf('AppBundle\Entity\ModFlowKernel', $modflowKernel);
    }
}
