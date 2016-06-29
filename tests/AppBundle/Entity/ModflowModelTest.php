<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\WellFactory;

class ModflowModelTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModFlowModel */
    protected $modflowModel;

    public function setUp()
    {
        $this->modflowModel = new ModFlowModel();
    }

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\ModFlowModel', $this->modflowModel);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->modflowModel->getId());
        $this->assertInstanceOf('\DateTime', $this->modflowModel->getDateCreated());
        $this->assertInstanceOf('\DateTime', $this->modflowModel->getDateModified());
        $this->assertTrue($this->modflowModel->getPublic());
    }

    public function testAddAndRemoveModelObjects(){
        $well = WellFactory::create()->setName('Well');
        $this->assertCount(0, $this->modflowModel->getModelObjects());
        $this->modflowModel->addModelObject($well);
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->modflowModel->addModelObject($well);
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->modflowModel->removeModelObject($well);
        $this->assertCount(0, $this->modflowModel->getModelObjects());
    }

    public function testSetAndGetArea(){
        $area = AreaFactory::create()->setName('Area');
        $this->modflowModel->setArea($area);
        $this->assertEquals($area, $this->modflowModel->getArea());
    }

    public function testSetAndGetActiveCells(){
        $activeCells = ActiveCells::fromArray(array(array(1,2,3), array(1,2,3)));
        $this->modflowModel->setActiveCells($activeCells);
        $this->assertEquals($activeCells, $this->modflowModel->getActiveCells());
    }

    public function testSetAndGetSoilModel(){
        $this->assertFalse($this->modflowModel->hasSoilModel());
        $soilModel = SoilModelFactory::create()->setName('SoilModel');
        $this->modflowModel->setSoilModel($soilModel);
        $this->assertTrue($this->modflowModel->hasSoilModel());
        $this->assertEquals($soilModel, $this->modflowModel->getSoilModel());
    }

    public function testAddAndRemoveBoundaries(){
        $well = WellFactory::create()->setName('Well');
        $this->assertCount(0, $this->modflowModel->getBoundaries());
        $this->modflowModel->addBoundary($well);
        $this->assertCount(1, $this->modflowModel->getBoundaries());
        $this->modflowModel->addBoundary($well);
        $this->assertCount(1, $this->modflowModel->getBoundaries());
        $this->modflowModel->removeBoundary($well);
        $this->assertCount(0, $this->modflowModel->getBoundaries());
    }

    public function testAddAndRemoveObservationPoints(){
        $observationPoint = ObservationPointFactory::create()->setName('ObservationPoint');
        $this->assertCount(0, $this->modflowModel->getObservationPoints());
        $this->modflowModel->addObservationPoint($observationPoint);
        $this->assertCount(1, $this->modflowModel->getObservationPoints());
        $this->modflowModel->addObservationPoint($observationPoint);
        $this->assertCount(1, $this->modflowModel->getObservationPoints());
        $this->modflowModel->removeObservationPoint($observationPoint);
        $this->assertCount(0, $this->modflowModel->getObservationPoints());
    }

    protected function tearDown()
    {
        unset($this->modflowModel);
    }
}
