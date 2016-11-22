<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\Factory\ChdPackageFactory;
use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;

class StressperiodsConstantHeadBoundaryTest extends \PHPUnit_Framework_TestCase
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

        $constantHeadBoundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2))
            ->setActiveCells(ActiveCells::fromArray([[false, true, false],[true, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createChd()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setShead(10)
                    ->setEhead(100),
                new Point(1,2,4)
            )
        ;

        $this->model->addBoundary($constantHeadBoundary);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var GeneralHeadBoundary $generalHeadBoundary */
        $constantHeadBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $constantHeadBoundary->getActiveCells());
        $this->assertCount(1, $constantHeadBoundary->getStressPeriods());

        $chdPackageFactory = new ChdPackageFactory();
        $chdPackage = $chdPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($chdPackage);
        $this->assertJson($json);

        $chdPackageData = json_decode($json);
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
            $chdPackageData->stress_period_data
        );

        $this->assertObjectHasAttribute('dtype', $chdPackageData);
        $this->assertObjectHasAttribute('options', $chdPackageData);
        $this->assertObjectHasAttribute('extension', $chdPackageData);
        $this->assertObjectHasAttribute('unitnumber', $chdPackageData);
    }

    public function testTwoStressperiodBoundariesWithOneObservationPoint(){

        $constantHeadBoundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(1,2))
            ->setActiveCells(ActiveCells::fromArray([[false, true, false],[true, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createChd()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setShead(10)
                    ->setEhead(100),
                new Point(1,2,4)
            )
        ;
        $this->model->addBoundary($constantHeadBoundary);

        $constantHeadBoundary = BoundaryFactory::createChd()
            ->setLayerNumbers(array(0))
            ->setActiveCells(ActiveCells::fromArray([[],[],[true, true, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createChd()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setShead(20)
                    ->setEhead(30),
                new Point(1,2,4)
            )
        ;
        $this->model->addBoundary($constantHeadBoundary);
        $this->assertCount(2, $this->model->getBoundaries());

        /** @var GeneralHeadBoundary $generalHeadBoundary */
        $generalHeadBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $generalHeadBoundary->getActiveCells());
        $this->assertCount(1, $generalHeadBoundary->getStressPeriods());

        $chdPackageFactory = new ChdPackageFactory();
        $chdPackage = $chdPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($chdPackage);
        $this->assertJson($json);

        $chdPackageData = json_decode($json);
        $this->assertEquals(
            (object) array(
                0 => [
                    [1, 0, 1, 10, 100],
                    [1, 1, 0, 10, 100],
                    [1, 1, 2, 10, 100],
                    [2, 0, 1, 10, 100],
                    [2, 1, 0, 10, 100],
                    [2, 1, 2, 10, 100],
                    [0, 2, 0, 20, 30],
                    [0, 2, 1, 20, 30],
                    [0, 2, 2, 20, 30]
                ]
            ),
            $chdPackageData->stress_period_data
        );

        $this->assertObjectHasAttribute('dtype', $chdPackageData);
        $this->assertObjectHasAttribute('options', $chdPackageData);
        $this->assertObjectHasAttribute('extension', $chdPackageData);
        $this->assertObjectHasAttribute('unitnumber', $chdPackageData);
    }

    public function tearDown() {
        unset($this->model);
    }
}
