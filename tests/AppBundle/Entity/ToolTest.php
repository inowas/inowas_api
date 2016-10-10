<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowKernel;
use AppBundle\Entity\Tool;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ToolFactory;

class ToolTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Tool */
    protected $tool;

    public function setUp()
    {
        $this->tool = ToolFactory::create();
    }

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\Tool', $this->tool);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->tool->getId());
    }

    public function testSetGetKernel(){
        $kernel = new ModFlowKernel();
        $this->tool->setKernel($kernel);
        $this->assertEquals($kernel, $this->tool->getKernel());
    }

    public function testSetGetModel(){
        $model = ModFlowModelFactory::create();
        $this->tool->setModel($model);
        $this->assertEquals($model, $this->tool->getModel());
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        unset($this->tool);
    }
}
