<?php

namespace Inowas\Soilmodel\Tests\Model;

use Inowas\Soilmodel\Model\Soilmodel;
use Inowas\Soilmodel\Model\SoilmodelFactory;

class SoilmodelFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testCreate(){
        $this->assertInstanceOf(Soilmodel::class, SoilmodelFactory::create());
    }
}
