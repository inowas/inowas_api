<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ChangeLayerValueEvent;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\PropertyValue;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;

class ChangeLayerValueEventTest extends \PHPUnit_Framework_TestCase
{

    /** @var  GeologicalLayer */
    protected $layer;

    /** @var  PropertyType */
    protected $propertyType;

    /** @var  PropertyValue */
    protected $value;

    /** @var  ChangeLayerValueEvent */
    protected $event;
    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->layer = GeologicalLayerFactory::create();
        $this->propertyType = PropertyTypeFactory::create(PropertyType::KX);
        $this->value = PropertyValueFactory::create()->setValue(2.2);
        $this->event = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
    }

    public function testInstantiateChangeLayerValueEvent()
    {
        $this->assertInstanceOf(ChangeLayerValueEvent::class, $this->event);
    }

    public function testGetLayer()
    {
        $this->assertEquals($this->layer, $this->event->getLayer());
    }

    public function testGetPropertyType()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertEquals($this->propertyType, $changeLayerValueEvent->getPropertyType());
    }

    public function testGetValue()
    {
        $changeLayerValueEvent = new ChangeLayerValueEvent($this->layer, $this->propertyType, $this->value);
        $this->assertEquals($this->value, $changeLayerValueEvent->getValue());
    }

    public function testApplyToModel(){
        $propertyType = PropertyTypeFactory::create(PropertyType::KX);
        $propertyValue = PropertyValueFactory::create()->setValue(1.1);

        $model = ModFlowModelFactory::create();
        $model->setSoilModel(SoilModelFactory::create()
            ->addGeologicalLayer(GeologicalLayerFactory::create()
                ->addValue($propertyType, $propertyValue))
        );

        $this->assertTrue($model->hasSoilModel());
        $this->assertTrue($model->getSoilModel()->hasGeologicalLayers());
        $this->assertEquals(1, $model->getSoilModel()->getNumberOfGeologicalLayers());

        /** @var GeologicalLayer $layer */
        $layer = $model->getSoilModel()->getGeologicalLayers()->first();
        $this->assertEquals($propertyValue, $layer->getPropertyByPropertyType($propertyType)->getValues()->first());

        /** @var ChangeLayerValueEvent $event */
        $event = new ChangeLayerValueEvent($layer, $this->propertyType, $this->value);
        $event->applyTo($model);

        $this->assertTrue($model->hasSoilModel());
        $this->assertTrue($model->getSoilModel()->hasGeologicalLayers());
        $this->assertEquals(1, $model->getSoilModel()->getNumberOfGeologicalLayers());
        $this->assertEquals($this->value, $layer->getPropertyByPropertyType($propertyType)->getValues()->first());
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {}
}
