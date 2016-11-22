<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\Factory\GhbPackageFactory;
use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;

class StressperiodsGeneralHeadBoundaryTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ModflowModel */
    private $model;

    public function setUp(){
        $this->model = ModflowModelFactory::create()
            ->setStart(new \DateTime('2010-01-01'))
            ->setEnd(new \DateTime('2015-12-31'))
        ;
    }

    public function testModelCreation(){
        $this->assertInstanceOf(ModflowModel::class, $this->model);
    }

    public function testOneStressperiodBoundaryWithOneObservationPoint(){

        $generalHeadBoundary = BoundaryFactory::createGhb()
            ->setLayerNumbers(array(1,2))
            ->setActiveCells(ActiveCells::fromArray([[false, true, false],[true, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createGhb()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setConductivity(100)
                    ->setStage(10),
                new Point(1,2,4)
            )
        ;

        $this->model->addBoundary($generalHeadBoundary);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var GeneralHeadBoundary $generalHeadBoundary */
        $generalHeadBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $generalHeadBoundary->getActiveCells());
        $this->assertCount(1, $generalHeadBoundary->getStressPeriods());

        $ghbPackageFactory = new GhbPackageFactory();
        $ghbPackage = $ghbPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($ghbPackage);
        $this->assertJson($json);

        $ghbPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $ghbPackageData);
        $this->assertEquals(
            (object) array(
                0 => [
                    [1, 0, 1, 10, 100],
                    [1, 1, 0, 10, 100],
                    [1, 1, 2, 10, 100],
                    [2, 0, 1, 10, 100],
                    [2, 1, 0, 10, 100],
                    [2, 1, 2, 10, 100]
                ]
            ),
            $ghbPackageData->stress_period_data
        );

        $this->assertObjectHasAttribute('dtype', $ghbPackageData);
        $this->assertObjectHasAttribute('options', $ghbPackageData);
        $this->assertObjectHasAttribute('extension', $ghbPackageData);
        $this->assertObjectHasAttribute('unitnumber', $ghbPackageData);
    }

    public function testTwoStressperiodBoundariesWithOneObservationPoint(){

        $generalHeadBoundary = BoundaryFactory::createGhb()
            ->setLayerNumbers(array(1,2))
            ->setActiveCells(ActiveCells::fromArray([[false, true, false],[true, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createGhb()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setConductivity(100)
                    ->setStage(10),
                new Point(1,2,4)
            )
        ;

        $this->model->addBoundary($generalHeadBoundary);

        $generalHeadBoundary = BoundaryFactory::createGhb()
            ->setLayerNumbers(array(0))
            ->setActiveCells(ActiveCells::fromArray([[],[],[true, true, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createGhb()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setConductivity(50)
                    ->setStage(5),
                new Point(1,2,4)
            )
        ;

        $this->model->addBoundary($generalHeadBoundary);

        $this->assertCount(2, $this->model->getBoundaries());

        /** @var GeneralHeadBoundary $generalHeadBoundary */
        $generalHeadBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $generalHeadBoundary->getActiveCells());
        $this->assertCount(1, $generalHeadBoundary->getStressPeriods());

        $ghbPackageFactory = new GhbPackageFactory();
        $ghbPackage = $ghbPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($ghbPackage);
        $this->assertJson($json);

        $ghbPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $ghbPackageData);
        $this->assertEquals(
            (object) array(
                0 => [
                    [1, 0, 1, 10, 100],
                    [1, 1, 0, 10, 100],
                    [1, 1, 2, 10, 100],
                    [2, 0, 1, 10, 100],
                    [2, 1, 0, 10, 100],
                    [2, 1, 2, 10, 100],
                    [0, 2, 0, 5, 50],
                    [0, 2, 1, 5, 50],
                    [0, 2, 2, 5, 50]
                ]
            ),
            $ghbPackageData->stress_period_data
        );

        $this->assertObjectHasAttribute('dtype', $ghbPackageData);
        $this->assertObjectHasAttribute('options', $ghbPackageData);
        $this->assertObjectHasAttribute('extension', $ghbPackageData);
        $this->assertObjectHasAttribute('unitnumber', $ghbPackageData);
    }

    public function tearDown() {
        unset($this->model);
    }

}