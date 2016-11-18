<?php

namespace Inowas\Soilmodel\Tests\Factory;

use Inowas\SoilmodelBundle\Model\Soilmodel;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;

class SoilmodelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(Soilmodel::class, SoilmodelFactory::create());
    }
}
