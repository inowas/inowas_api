<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowKernel;

class AbstractKernelTest extends \PHPUnit_Framework_TestCase
{
    public function testCreateId()
    {
        $modflowKernel = new ModFlowKernel();
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $modflowKernel->getId());
    }
}
