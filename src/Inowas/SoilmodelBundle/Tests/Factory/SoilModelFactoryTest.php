<?php

namespace Inowas\Soilmodel\Tests\Factory;

use Inowas\Soilmodel\Model\Soilmodel;
use Inowas\Soilmodel\Factory\SoilmodelFactory;

class SoilmodelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(Soilmodel::class, SoilmodelFactory::create());
    }
}
