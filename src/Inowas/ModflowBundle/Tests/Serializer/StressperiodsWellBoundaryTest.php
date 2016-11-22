<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\Factory\WelPackageFactory;
use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;

class StressperiodsWellBoundaryTest extends \PHPUnit_Framework_TestCase
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

    public function testWellBoundaryWithOneWell(){
        $this->model->addBoundary(
            BoundaryFactory::createWel()
                ->setLayerNumber(2)
                ->setGeometry(new Point(1,2,3))
                ->setActiveCells(ActiveCells::fromArray([[],[false, false, true]]))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-01-01'))
                        ->setFlux(1.1))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-02-01'))
                        ->setFlux(2.2))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-03-01'))
                        ->setFlux(3.3))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-04-01'))
                        ->setFlux(4.4))
        );

        $this->assertCount(1, $this->model->getBoundaries());

        /** @var WellBoundary $wellBoundary */
        $wellBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $wellBoundary->getActiveCells());
        $this->assertCount(4, $wellBoundary->getStressPeriods());

        $wellPackageFactory = new WelPackageFactory();
        $wellPackage = $wellPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($wellPackage);
        $this->assertJson($json);

        $wellPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $wellPackageData);
        $this->assertObjectHasAttribute('stress_period_data', $wellPackageData);

        $this->assertEquals(
            $wellPackageData->stress_period_data,
            (object) array(
                0 => [[2, 1, 2, 1.1]],
                1 => [[2, 1, 2, 2.2]],
                2 => [[2, 1, 2, 3.3]],
                3 => [[2, 1, 2, 4.4]]
            )
        );

        $this->assertObjectHasAttribute('dtype', $wellPackageData);
        $this->assertObjectHasAttribute('options', $wellPackageData);
        $this->assertObjectHasAttribute('extension', $wellPackageData);
        $this->assertObjectHasAttribute('unitnumber', $wellPackageData);
    }

    public function testWellBoundaryWithThreeWells(){
        $this->model->addBoundary(
            BoundaryFactory::createWel()
                ->setLayerNumber(2)
                ->setActiveCells(ActiveCells::fromArray([[],[false, false, true]]))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-01-01'))
                        ->setFlux(1.1))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-02-01'))
                        ->setFlux(2.2))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-03-01'))
                        ->setFlux(3.3))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-04-01'))
                        ->setFlux(4.4))
        );

        $this->model->addBoundary(
            BoundaryFactory::createWel()
                ->setLayerNumber(2)
                ->setActiveCells(ActiveCells::fromArray([[],[false, false, false, false, true]]))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-01-01'))
                        ->setFlux(11.1))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-02-01'))
                        ->setFlux(22.2))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-03-01'))
                        ->setFlux(33.3))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-04-01'))
                        ->setFlux(44.4))
        );

        $this->model->addBoundary(
            BoundaryFactory::createWel()
                ->setLayerNumber(2)
                ->setActiveCells(ActiveCells::fromArray([[],[false, false, false, false, false, false, true]]))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-01-01'))
                        ->setFlux(111.1))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-02-01'))
                        ->setFlux(222.2))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-03-01'))
                        ->setFlux(333.3))
                ->addStressPeriod(
                    StressPeriodFactory::createWel()
                        ->setDateTimeBegin(new \DateTime('2010-04-01'))
                        ->setFlux(444.4))
        );

        $this->assertCount(3, $this->model->getBoundaries());

        $wellPackageFactory = new WelPackageFactory();
        $wellPackage = $wellPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($wellPackage);
        $this->assertJson($json);

        $wellPackageData = json_decode($json);
        $this->assertObjectHasAttribute('ipakcb', $wellPackageData);
        $this->assertObjectHasAttribute('stress_period_data', $wellPackageData);

        $this->assertEquals(
            $wellPackageData->stress_period_data,
            (object) array(
                0 => [
                    [2, 1, 2, 1.1],
                    [2, 1, 4, 11.1],
                    [2, 1, 6, 111.1],

                ],
                1 => [
                    [2, 1, 2, 2.2],
                    [2, 1, 4, 22.2],
                    [2, 1, 6, 222.2]
                ],
                2 => [
                    [2, 1, 2, 3.3],
                    [2, 1, 4, 33.3],
                    [2, 1, 6, 333.3]
                ],
                3 => [
                    [2, 1, 2, 4.4],
                    [2, 1, 4, 44.4],
                    [2, 1, 6, 444.4],
                ]
            )
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