<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\Factory\RivPackageFactory;
use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;

class StressperiodsRiverBoundaryTest extends \PHPUnit_Framework_TestCase
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

    public function testOneRiverBoundaryWithOneObservationPoint(){

        $riverBoundary = BoundaryFactory::createRiv()
            ->setActiveCells(ActiveCells::fromArray([[false, true, true],[false, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setBottomElevation(10)
                    ->setStage(12)
                    ->setConductivity(100.1),
                new Point(1,2,4)
            );

        $this->model->addBoundary($riverBoundary);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var RiverBoundary $riverBoundary */
        $riverBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $riverBoundary->getActiveCells());
        $this->assertCount(1, $riverBoundary->getStressPeriods());

        $riverPackageFactory = new RivPackageFactory();
        $riverPackage = $riverPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($riverPackage);
        $this->assertJson($json);

        $riverPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $riverPackageData);
        $this->assertEquals(
            $riverPackageData->stress_period_data,
            (object) array(
                0 => [
                    [0, 0, 1, 12, 100.1, 10],
                    [0, 0, 2, 12, 100.1, 10],
                    [0, 1, 2, 12, 100.1, 10]
                ]
            )
        );

        $this->assertObjectHasAttribute('dtype', $riverPackageData);
        $this->assertObjectHasAttribute('options', $riverPackageData);
        $this->assertObjectHasAttribute('extension', $riverPackageData);
        $this->assertObjectHasAttribute('unitnumber', $riverPackageData);
    }

    public function testOneRiverBoundaryWithOneObservationPointAndTwoStressperiods(){

        $riverBoundary = BoundaryFactory::createRiv()
            ->setActiveCells(ActiveCells::fromArray([[false, true, true],[false, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setBottomElevation(10)
                    ->setStage(12)
                    ->setConductivity(100.1),
                new Point(1,2,4)
            )
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-06-01'))
                    ->setBottomElevation(8)
                    ->setStage(10)
                    ->setConductivity(200.2),
                new Point(1,2,4)
            );

        $this->model->addBoundary($riverBoundary);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var RiverBoundary $riverBoundary */
        $riverBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $riverBoundary->getActiveCells());
        $this->assertCount(2, $riverBoundary->getStressPeriods());

        $riverPackageFactory = new RivPackageFactory();
        $riverPackage = $riverPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($riverPackage);
        $this->assertJson($json);

        $riverPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $riverPackageData);
        $this->assertObjectHasAttribute('stress_period_data', $riverPackageData);

        $this->assertEquals(
            (object) array(
                0 => [
                    [0, 0, 1, 12, 100.1, 10],
                    [0, 0, 2, 12, 100.1, 10],
                    [0, 1, 2, 12, 100.1, 10]
                ],
                1 => [
                    [0, 0, 1, 10, 200.2, 8],
                    [0, 0, 2, 10, 200.2, 8],
                    [0, 1, 2, 10, 200.2, 8]
                ],
            ),
            $riverPackageData->stress_period_data
        );

        $this->assertObjectHasAttribute('dtype', $riverPackageData);
        $this->assertObjectHasAttribute('options', $riverPackageData);
        $this->assertObjectHasAttribute('extension', $riverPackageData);
        $this->assertObjectHasAttribute('unitnumber', $riverPackageData);
    }

    public function testTwoRiverBoundariesWithTwoStressperiodsOneObservationPointEach(){

        $riverBoundary = BoundaryFactory::createRiv()
            ->setActiveCells(ActiveCells::fromArray([[false, true, true],[false, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setBottomElevation(10)
                    ->setStage(12)
                    ->setConductivity(100.1),
                new Point(1,2,4)
            )
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-04-01'))
                    ->setBottomElevation(11)
                    ->setStage(13)
                    ->setConductivity(111),
                new Point(1,2,4)
            )
        ;

        $this->model->addBoundary($riverBoundary);

        $riverBoundary = BoundaryFactory::createRiv()
            ->setActiveCells(ActiveCells::fromArray([[],[],[],[false, true, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setBottomElevation(12)
                    ->setStage(14)
                    ->setConductivity(112),
                new Point(1,2,4)
            )
            ->addStressPeriod(
                StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('2010-06-01'))
                    ->setBottomElevation(13)
                    ->setStage(15)
                    ->setConductivity(113),
                new Point(1,2,4)
            )
        ;

        $this->model->addBoundary($riverBoundary);
        $this->assertCount(2, $this->model->getBoundaries());

        /** @var RiverBoundary $riverBoundary */
        $riverBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $riverBoundary->getActiveCells());
        $this->assertCount(2, $riverBoundary->getStressPeriods());


        $riverPackageFactory = new RivPackageFactory();
        $riverPackage = $riverPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($riverPackage);
        $this->assertJson($json);

        $wellPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $wellPackageData);
        $this->assertObjectHasAttribute('dtype', $wellPackageData);
        $this->assertObjectHasAttribute('options', $wellPackageData);
        $this->assertObjectHasAttribute('naux', $wellPackageData);
        $this->assertObjectHasAttribute('extension', $wellPackageData);
        $this->assertObjectHasAttribute('unitnumber', $wellPackageData);
        $this->assertObjectHasAttribute('stress_period_data', $wellPackageData);

        $this->assertEquals(
            (object) array(
                0 => [
                    [0, 0, 1, 12, 100.1, 10],
                    [0, 0, 2, 12, 100.1, 10],
                    [0, 1, 2, 12, 100.1, 10],
                    [0, 3, 1, 14, 112, 12],
                    [0, 3, 2, 14, 112, 12]
                ],
                1 => [
                    [0, 0, 1, 13, 111, 11],
                    [0, 0, 2, 13, 111, 11],
                    [0, 1, 2, 13, 111, 11]
                ],
                2 => [
                    [0, 3, 1, 15, 113, 13],
                    [0, 3, 2, 15, 113, 13]
                ],
            ),
            $wellPackageData->stress_period_data
        );

        $this->assertObjectHasAttribute('dtype', $wellPackageData);
        $this->assertObjectHasAttribute('options', $wellPackageData);
        $this->assertObjectHasAttribute('extension', $wellPackageData);
        $this->assertObjectHasAttribute('unitnumber', $wellPackageData);
    }

    public function tearDown() {
        unset($this->model);
    }

}