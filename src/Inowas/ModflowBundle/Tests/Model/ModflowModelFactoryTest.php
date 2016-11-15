<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Model\ModFlowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;

class ModflowModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(ModFlowModel::class, ModflowModelFactory::create());
    }

}
