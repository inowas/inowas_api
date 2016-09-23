<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use AppBundle\Model\ModelScenarioFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\StressPeriod;
use AppBundle\Model\StressPeriodFactory;
use AppBundle\Model\WellBoundaryFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationPropertiesFactory;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

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
        $well = WellBoundaryFactory::create()->setName('Well');
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
        $this->modflowModel->setArea(AreaFactory::create());
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
        $well = WellBoundaryFactory::create()->setName('Well');
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

    public function testLoadStressPeriodsFromBoundaries(){
        $this->modflowModel
            ->addBoundary(
                WellBoundaryFactory::createPrivateWell()
                    ->addStressPeriod(
                        StressPeriodFactory::createWel()
                            ->setDateTimeBegin(new \DateTime('1.1.2015'))
                            ->setDateTimeEnd(new \DateTime('7.1.2015')))
                    ->addStressPeriod(
                        StressPeriodFactory::createWel()
                            ->setDateTimeBegin(new \DateTime('8.1.2015'))
                            ->setDateTimeEnd(new \DateTime('13.1.2015')))
                    ->addStressPeriod(
                        StressPeriodFactory::createWel()
                            ->setDateTimeBegin(new \DateTime('14.1.2015'))
                            ->setDateTimeEnd(new \DateTime('22.1.2015')))
                    ->addStressPeriod(
                        StressPeriodFactory::createWel()
                            ->setDateTimeBegin(new \DateTime('23.1.2015'))
                            ->setDateTimeEnd(new \DateTime('27.1.2015')))
                    ->addStressPeriod(
                        StressPeriodFactory::createWel()
                            ->setDateTimeBegin(new \DateTime('28.1.2015'))
                            ->setDateTimeEnd(new \DateTime('29.1.2015')))
            )
            ->addBoundary(
                StreamBoundaryFactory::create()
                    ->setActiveCells(ActiveCells::fromArray(array(
                        array(0,0,0,1,0,0,0),
                        array(0,0,1,1,1,0,0),
                        array(0,1,1,1,1,1,0),
                        array(0,0,1,1,1,0,0),
                        array(0,0,0,1,0,0,0),
                        array(0,0,1,0,1,0,0),
                    )))
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('1.1.2015'))
                            ->setDateTimeEnd(new \DateTime('2.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('3.1.2015'))
                            ->setDateTimeEnd(new \DateTime('6.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('6.1.2015'))
                            ->setDateTimeEnd(new \DateTime('9.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('10.1.2015'))
                            ->setDateTimeEnd(new \DateTime('12.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('13.1.2015'))
                            ->setDateTimeEnd(new \DateTime('16.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('17.1.2015'))
                            ->setDateTimeEnd(new \DateTime('20.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('21.1.2015'))
                            ->setDateTimeEnd(new \DateTime('22.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('23.1.2015'))
                            ->setDateTimeEnd(new \DateTime('24.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('25.1.2015'))
                            ->setDateTimeEnd(new \DateTime('25.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('26.1.2015'))
                            ->setDateTimeEnd(new \DateTime('27.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
                    ->addStressPeriod(
                        StressPeriodFactory::createRiv()
                            ->setDateTimeBegin(new \DateTime('28.1.2015'))
                            ->setDateTimeEnd(new \DateTime('29.1.2015'))
                            ->setStage(1.1)
                            ->setCond(11.1)
                            ->setRbot(111.1)
                    )
            )
        ;

        $this->assertCount(13, $this->modflowModel->getStressPeriods());
        $this->assertInstanceOf(ArrayCollection::class, $this->modflowModel->getStressPeriods());

        foreach ($this->modflowModel->getStressPeriods() as $sp){
            $this->assertInstanceOf(StressPeriod::class, $sp);
        }

        #var_dump($this->modflowModel->getBoundaries()->toArray()[1]->getStressPeriodData($this->modflowModel->getStressPeriods()));

        /** @var StressPeriod $sp */
        $sp = $this->modflowModel->getStressPeriods()->first();
        $this->assertEquals(new \DateTime('1.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('2.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('3.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('5.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('6.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('7.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('8.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('9.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('10.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('12.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('13.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('13.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('14.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('16.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('17.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('20.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('21.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('22.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('23.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('24.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('25.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('25.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('26.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('27.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());

        $sp = $this->modflowModel->getStressPeriods()->next();
        $this->assertEquals(new \DateTime('28.1.2015'), $sp->getDateTimeBegin());
        $this->assertEquals(new \DateTime('29.1.2015'), $sp->getDateTimeEnd());
        $this->assertFalse($sp->isSteady());
    }

    public function testSetGetCalculationProperties(){
        $properties = FlopyCalculationPropertiesFactory::loadFromApiAndRun($this->modflowModel);
        $this->modflowModel->setCalculationProperties($properties);
        $this->assertEquals($properties, $this->modflowModel->getCalculationProperties());
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

    public function testSetGetHeads(){
        $heads = array(1 => Flopy3DArray::fromValue(1,1,1,1));
        $this->modflowModel->setHeads($heads);
        $this->assertEquals($heads, $this->modflowModel->getHeads());
    }

    public function testPreFlush()
    {
        $area = AreaFactory::create()->setName('Area');
        $this->modflowModel->setArea($area);
        $this->modflowModel->preFlush();
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->assertEquals($area, $this->modflowModel->getModelObjects()->first());

        // Reset
        $this->modflowModel->removeModelObject($area);
        $this->modflowModel->setArea();
        $this->assertCount(0, $this->modflowModel->getModelObjects());

        // Add Well
        $well = WellBoundaryFactory::create();
        $this->modflowModel->addBoundary($well);
        $this->modflowModel->preFlush();
        $this->assertCount(0, $this->modflowModel->getBoundaries());
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->assertEquals($well, $this->modflowModel->getModelObjects()->first());

        // Reset
        $this->modflowModel->removeModelObject($well);
        $this->assertCount(0, $this->modflowModel->getModelObjects());

        // Add ObservationPoint
        $observationPoint = ObservationPointFactory::create();
        $this->modflowModel->addObservationPoint($observationPoint);
        $this->modflowModel->preFlush();
        $this->assertCount(0, $this->modflowModel->getObservationPoints());
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->assertEquals($observationPoint, $this->modflowModel->getModelObjects()->first());

    }

    public function testPostLoad(){
        $area = AreaFactory::create()->setName('Area');
        $this->modflowModel->addModelObject($area);
        $this->assertCount(1, $this->modflowModel->getModelObjects());
        $this->modflowModel->postLoad();
        $this->assertEquals($area, $this->modflowModel->getArea());
        $this->assertCount(0, $this->modflowModel->getModelObjects());

        $well = WellBoundaryFactory::create();
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
