<?php

namespace Inowas\SoilmodelBundle\Tests\Model;


use Inowas\Soilmodel\Model\BoreHoleLayer;
use Inowas\SoilmodelBundle\Model\BoreHoleLayerFactory;

class BoreHoleLayerFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate(){
        $this->assertInstanceOf(BoreHoleLayer::class, BoreHoleLayerFactory::create());
    }

}
