<?php

namespace Inowas\SoilmodelBundle\Tests\Model;

use Inowas\Soilmodel\Model\Layer;
use Inowas\SoilmodelBundle\Model\LayerFactory;

class LayerFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function setUp(){}

    public function testCreate(){
        $this->assertInstanceOf(Layer::class, LayerFactory::create());
    }

}
