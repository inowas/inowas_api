<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StressPeriodFactory;
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

    public function testAddSetGetStressperiods(){
        $stressPeriod = StressPeriodFactory::create();
        $this->assertCount(0, $this->modflowModel->getStressPeriods());
        $this->modflowModel->addStressPeriod($stressPeriod);
        $this->assertCount(1, $this->modflowModel->getStressPeriods());
        $this->modflowModel->addStressPeriod($stressPeriod);
        $this->assertCount(2, $this->modflowModel->getStressPeriods());

        $stressPeriods = $this->modflowModel->getStressPeriods();
        $stressPeriods[] = $stressPeriod;

        $this->modflowModel->setStressPeriods($stressPeriods);
        $this->assertCount(3, $this->modflowModel->getStressPeriods());
    }

    public function testSetGetCalculationProperties(){
        $calculationProperties = $this->modflowModel->getCalculationProperties();
        $this->modflowModel->setCalculationProperties($calculationProperties);
    }

    public function testSetGetGridSize(){
        $gridSize = new GridSize(10, 11);
        $this->modflowModel->setGridSize($gridSize);
        $this->assertEquals($gridSize, $this->modflowModel->getGridSize());
    }

    public function testSetGetBoundingBox(){
        $boundingBox = new BoundingBox(10, 11, 12, 14, 4326);
        $this->modflowModel->setBoundingBox($boundingBox);
        $this->assertEquals($boundingBox, $this->modflowModel->getBoundingBox());
    }

    public function testCreateOverview(){
        $this->assertEquals("50 Rows, 50 Columns, 0 Layers", $this->modflowModel->createTextOverview());
        $this->modflowModel->setGridSize(null);
        $this->assertEquals("0 Rows, 0 Columns, 0 Layers", $this->modflowModel->createTextOverview());
        $this->modflowModel->setSoilModel(SoilModelFactory::create()
            ->addGeologicalLayer(GeologicalLayerFactory::create()));
        $this->assertEquals("0 Rows, 0 Columns, 1 Layers", $this->modflowModel->createTextOverview());

    }

    public function testRegisterScenarios()
    {
        $this->assertCount(0, $this->modflowModel->getScenarios());
        $this->modflowModel->registerScenario(ModelScenarioFactory::create($this->modflowModel));
        $this->assertCount(1, $this->modflowModel->getScenarios());
    }

    public function testSetInitValues()
    {
        $initValues = array(1,2,3);
        $this->modflowModel->setInitialValues($initValues);
        $this->assertEquals($initValues, $this->modflowModel->getInitialValues());
    }

    public function testPostLoad(){
        $area = AreaFactory::create()->setName('Area');
        $this->modflowModel->addModelObject($area);
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->modflowModel->postLoad();
        $this->assertEquals($area, $this->modflowModel->getArea());
        $this->assertCount(0, $this->modflowModel->getModelObjects());

        $well = WellFactory::create();
        $this->modflowModel->addModelObject($well);
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->modflowModel->postLoad();
        $this->assertCount(1, $this->modflowModel->getBoundaries());
        $this->assertEquals($well, $this->modflowModel->getBoundaries()->first());
        $this->assertCount(0, $this->modflowModel->getModelObjects());

        $observationPoint = ObservationPointFactory::create();
        $this->modflowModel->addModelObject($observationPoint);
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->modflowModel->postLoad();
        $this->assertCount(1, $this->modflowModel->getObservationPoints());
        $this->assertEquals($observationPoint, $this->modflowModel->getObservationPoints()->first());
        $this->assertCount(0, $this->modflowModel->getModelObjects());

    }

    protected function tearDown()
    {
        unset($this->modflowModel);
    }
}
