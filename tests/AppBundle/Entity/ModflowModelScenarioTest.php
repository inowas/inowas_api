<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AddBoundaryEvent;
use AppBundle\Entity\AddCalculationPropertiesEvent;
use AppBundle\Entity\ChangeBoundaryEvent;
use AppBundle\Entity\ModflowModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\RemoveBoundaryEvent;
use AppBundle\Model\EventFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\WellBoundaryFactory;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Ramsey\Uuid\Uuid;

class ModflowModelScenarioTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModFlowModel */
    protected $model;

    /** @var  ModflowModelScenario */
    protected $scenario;

    public function setUp()
    {
        $this->model = ModFlowModelFactory::create();
        $this->scenario = ModelScenarioFactory::create($this->model);
    }

    public function testInstantiate()
    {
        $this->assertInstanceOf(ModflowModelScenario::class, $this->scenario);
        $this->assertInstanceOf(Uuid::class, $this->scenario->getId());
        $this->assertInstanceOf(\DateTime::class, $this->scenario->getDateCreated());
        $this->assertInstanceOf(\DateTime::class, $this->scenario->getDateModified());
        $this->assertEquals($this->model->getId(), $this->scenario->getBaseModel()->getId());
    }

    public function testUpdateDateTimeModified()
    {
        $this->scenario->updateDateModified();
        $this->assertInstanceOf(\DateTime::class, $this->scenario->getDateModified());
    }

    public function testSetGetName()
    {
        $name = "name";
        $this->scenario->setName($name);
        $this->assertEquals($name, $this->scenario->getName());
    }

    public function testSetGetDescription()
    {
        $description = "description";
        $this->scenario->setDescription($description);
        $this->assertEquals($description, $this->scenario->getDescription());
    }

    public function testSetGetImageFileName()
    {
        $file = "fileName";
        $this->scenario->setImageFile($file);
        $this->assertEquals($file, $this->scenario->getImageFile());
    }

    public function testAddGetRemoveEvents()
    {
        $event = EventFactory::createAddBoundaryEvent(WellBoundaryFactory::create());
        $this->assertCount(0, $this->scenario->getEvents());
        $this->scenario->addEvent($event);
        $this->assertCount(1, $this->scenario->getEvents());
        $this->scenario->addEvent($event);
        $this->assertCount(1, $this->scenario->getEvents());
        $anotherEvent = EventFactory::createAddBoundaryEvent(WellBoundaryFactory::create());
        $this->scenario->addEvent($anotherEvent);
        $this->assertCount(2, $this->scenario->getEvents());
        $this->scenario->removeEvent($event);
        $this->scenario->removeEvent($anotherEvent);
        $this->assertCount(0, $this->scenario->getEvents());
    }

    public function testGetModelWithAppliedEvents()
    {
        $this->model->setSoilModel(SoilModelFactory::create()
            ->setName('SoilModel')
            ->addGeologicalLayer(GeologicalLayerFactory::create())
        );

        $event = EventFactory::createAddBoundaryEvent(WellBoundaryFactory::create());
        $this->scenario->addEvent($event);
        $event = EventFactory::createChangeLayerValueEvent(
            $this->model->getSoilModel()->getGeologicalLayers()->first(),
            PropertyTypeFactory::create(PropertyType::KX),
            PropertyValueFactory::create()
        );
        $this->scenario->addEvent($event);
        $this->assertInstanceOf(ModFlowModel::class, $this->scenario->getModel());
    }

    public function testIsModelScenario(){
        $this->assertTrue($this->scenario->isModelScenario());
    }

    public function testAddBoundaryToScenario(){
        $boundary = WellBoundaryFactory::createIndustrialWell();
        $this->assertCount(0, $this->scenario->getEvents());
        $this->scenario->addBoundary($boundary);
        $this->assertCount(1, $this->scenario->getEvents());
        $this->assertInstanceOf(AddBoundaryEvent::class, $this->scenario->getEvents()->first());
        $this->assertEquals($boundary, $this->scenario->getEvents()->first()->getBoundary());
    }

    public function testChangeBoundaryOfScenario(){
        $boundary = WellBoundaryFactory::createIndustrialWell();
        $newBoundary = WellBoundaryFactory::createPrivateWell();
        $this->assertCount(0, $this->scenario->getEvents());
        $this->scenario->changeBoundary($boundary, $newBoundary);
        $this->assertCount(1, $this->scenario->getEvents());
        $this->assertInstanceOf(ChangeBoundaryEvent::class, $this->scenario->getEvents()->first());
        $this->assertEquals($boundary, $this->scenario->getEvents()->first()->getOrigin());
        $this->assertEquals($newBoundary, $this->scenario->getEvents()->first()->getNewBoundary());
    }

    public function testRemoveBoundaryFromScenario(){
        $boundary = WellBoundaryFactory::createIndustrialWell();
        $this->assertCount(0, $this->scenario->getEvents());
        $this->scenario->removeBoundary($boundary);
        $this->assertCount(1, $this->scenario->getEvents());
        $this->assertInstanceOf(RemoveBoundaryEvent::class, $this->scenario->getEvents()->first());
        $this->assertEquals($boundary, $this->scenario->getEvents()->first()->getElement());
    }

    public function testAddCalculationProperties(){
        $calculationProperties = FlopyCalculationPropertiesFactory::loadFromApiAndRun($this->scenario->getBaseModel());
        $this->assertCount(0, $this->scenario->getEvents());
        $this->scenario->addCalculationProperties($calculationProperties);
        $this->assertCount(1, $this->scenario->getEvents());
        $this->assertInstanceOf(AddCalculationPropertiesEvent::class, $this->scenario->getEvents()->first());
        $this->assertEquals($calculationProperties, $this->scenario->getEvents()->first()->getCalculationProperties());
    }
}
