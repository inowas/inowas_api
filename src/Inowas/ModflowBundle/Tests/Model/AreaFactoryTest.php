<?php

namespace Inowas\ModflowBundle\Tests\Model;

use Inowas\ModflowBundle\Model\Area;
use Inowas\ModflowBundle\Model\AreaFactory;

class AreaFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(Area::class, AreaFactory::create());
    }
}
