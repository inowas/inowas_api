<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;

class ModflowModelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(ModflowModel::class, ModflowModelFactory::create());
    }

}
