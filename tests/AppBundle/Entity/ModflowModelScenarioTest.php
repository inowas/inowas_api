<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModelScenario;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\EventFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\WellBoundaryFactory;

class ModflowModelScenarioTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModFlowModel */
    protected $model;

    /** @var  ModelScenario */
    protected $scenario;

    public function setUp()
    {
        $this->model = ModFlowModelFactory::create();
        $this->scenario = ModelScenarioFactory::create($this->model);
    }

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\ModelScenario', $this->scenario);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->scenario->getId());
        $this->assertInstanceOf('\DateTime', $this->scenario->getDateCreated());
        $this->assertInstanceOf('\DateTime', $this->scenario->getDateModified());
        $this->assertEquals($this->model->getId(), $this->scenario->getBaseModel()->getId());
    }

    public function testUpdateDateTimeModified()
    {
        $this->scenario->updateDateModified();
        $this->assertInstanceOf('\DateTime', $this->scenario->getDateModified());
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

    public function testGetModelwithAppliedEvents()
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
}
