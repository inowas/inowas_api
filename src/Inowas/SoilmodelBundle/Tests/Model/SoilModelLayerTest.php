<?php

namespace Inowas\Soilmodel\Tests\Model;

use Inowas\Soilmodel\Model\Layer;
use Inowas\Soilmodel\Model\Property;
use Inowas\Soilmodel\Model\PropertyType;
use Inowas\Soilmodel\Model\PropertyValue;

class SoilmodelLayerTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Layer $soilModelLayer */
    private $soilModelLayer;

    public function setUp(){
        $this->soilModelLayer = new Layer();
    }

    public function testInstantiateWithOrderNumber0(){
        $this->assertInstanceOf(Layer::class, $this->soilModelLayer);
        $this->assertEquals(0, $this->soilModelLayer->getOrder());
    }

    public function testSetGetOrder(){
        $this->soilModelLayer->setOrder(10);
        $this->assertEquals(10, $this->soilModelLayer->getOrder());
    }

    public function testAddOrReplaceProperty(){
        $soilmodelProperty = new Property(
            PropertyType::fromString('kx'),
            PropertyValue::fromValue(array([1,2,3], [1,2,3]))
        );

        $this->soilModelLayer->addOrReplaceProperty($soilmodelProperty);
        $this->assertCount(1, $this->soilModelLayer->getProperties());
        $this->soilModelLayer->addOrReplaceProperty($soilmodelProperty);
        $this->assertCount(1, $this->soilModelLayer->getProperties());

        $soilmodelProperty = new Property(
            PropertyType::fromString('ky'),
            PropertyValue::fromValue(array([1,2,3], [1,2,3]))
        );

        $this->soilModelLayer->addOrReplaceProperty($soilmodelProperty);
        $this->assertCount(2, $this->soilModelLayer->getProperties());
    }

    public function tearDown()
    {
        unset($this->soilModelLayer);
    }
}
