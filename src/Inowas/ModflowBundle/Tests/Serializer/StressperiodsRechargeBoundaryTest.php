<?php

namespace Inowas\ModflowBundle\Tests\Serializer;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\Flopy\Model\Factory\RchPackageFactory;
use Inowas\ModflowBundle\Model\ActiveCells;
use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;

class StressperiodsRechargeBoundaryTest extends \PHPUnit_Framework_TestCase
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

    public function testOneRechargeBoundary(){

        $rechargeBoundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                array(
                    new Point(1,2),
                    new Point(2,2),
                    new Point(2,1),
                    new Point(1,1),
                    new Point(1,2)
                )
            )))
            ->setActiveCells(ActiveCells::fromArray([[false, true, true],[false, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setRecharge(12.3)
            );

        $this->model->addBoundary($rechargeBoundary);
        $this->assertCount(1, $this->model->getBoundaries());

        /** @var RiverBoundary $riverBoundary */
        $rechargeBoundary = $this->model->getBoundaries()->first();
        $this->assertInstanceOf(ActiveCells::class, $rechargeBoundary->getActiveCells());
        $this->assertCount(1, $rechargeBoundary->getStressPeriods());

        $rchPackageFactory = new RchPackageFactory();
        $rchPackage = $rchPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($rchPackage);
        $this->assertJson($json);
        $riverPackageData = json_decode($json);
        $this->assertObjectHasAttribute('nrchop', $riverPackageData);
        $this->assertObjectHasAttribute('ipakcb', $riverPackageData);
        $this->assertObjectHasAttribute('rech', $riverPackageData);

        $rech = json_decode(json_encode($riverPackageData->rech), true);
        $expected = array();
        $expected[0][1] = 12.3;
        $expected[0][2] = 12.3;
        $expected[1][2] = 12.3;

        $this->assertEquals(
            array(0 => $expected),
            $rech
        );

        $this->assertObjectHasAttribute('irch', $riverPackageData);
        $this->assertObjectHasAttribute('extension', $riverPackageData);
        $this->assertObjectHasAttribute('unitnumber', $riverPackageData);
    }

    public function testTwoRechargeBoundaries(){

        $rechargeBoundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                array(
                    new Point(1,2),
                    new Point(2,2),
                    new Point(2,1),
                    new Point(1,1),
                    new Point(1,2)
                )
            )))
            ->setActiveCells(ActiveCells::fromArray([[false, true, true],[false, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setRecharge(12.3)
            );

        $this->model->addBoundary($rechargeBoundary);

        $rechargeBoundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                array(
                    new Point(2,2),
                    new Point(2,1),
                    new Point(1,1),
                    new Point(1,2),
                    new Point(2,2)
                )
            )))
            ->setActiveCells(ActiveCells::fromArray([[false, false, false],[false, false, false], [true, true, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setRecharge(1.3)
            );

        $this->model->addBoundary($rechargeBoundary);
        $this->assertCount(2, $this->model->getBoundaries());

        $rchPackageFactory = new RchPackageFactory();
        $rchPackage = $rchPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($rchPackage);
        $this->assertJson($json);
        $riverPackageData = json_decode($json);
        $this->assertObjectHasAttribute('nrchop', $riverPackageData);
        $this->assertObjectHasAttribute('ipakcb', $riverPackageData);
        $this->assertObjectHasAttribute('rech', $riverPackageData);

        $rech = json_decode(json_encode($riverPackageData->rech), true);
        $expected = array();
        $expected[0][1] = 12.3;
        $expected[0][2] = 12.3;
        $expected[1][2] = 12.3;
        $expected[2][0] = 1.3;
        $expected[2][1] = 1.3;
        $expected[2][2] = 1.3;

        $this->assertEquals(
            array(0 => $expected),
            $rech
        );

        $this->assertObjectHasAttribute('irch', $riverPackageData);
        $this->assertObjectHasAttribute('extension', $riverPackageData);
        $this->assertObjectHasAttribute('unitnumber', $riverPackageData);
    }

    public function testTwoRechargeBoundariesWithTwoStressperiodsEach(){

        $rechargeBoundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                array(
                    new Point(1,2),
                    new Point(2,2),
                    new Point(2,1),
                    new Point(1,1),
                    new Point(1,2)
                )
            )))
            ->setActiveCells(ActiveCells::fromArray([[false, true, true],[false, false, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setRecharge(1.1)
            )
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-06-01'))
                    ->setRecharge(1.2)
            )
        ;

        $this->model->addBoundary($rechargeBoundary);

        $rechargeBoundary = BoundaryFactory::createRch()
            ->setGeometry(new Polygon(array(
                array(
                    new Point(2,2),
                    new Point(2,1),
                    new Point(1,1),
                    new Point(1,2),
                    new Point(2,2)
                )
            )))
            ->setActiveCells(ActiveCells::fromArray([[false, false, false],[false, false, false], [true, true, true]]))
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-01-01'))
                    ->setRecharge(2.1)
            )
            ->addStressPeriod(
                StressPeriodFactory::createRch()
                    ->setDateTimeBegin(new \DateTime('2010-06-01'))
                    ->setRecharge(2.2)
            );

        $this->model->addBoundary($rechargeBoundary);
        $this->assertCount(2, $this->model->getBoundaries());

        $rchPackageFactory = new RchPackageFactory();
        $rchPackage = $rchPackageFactory->create($this->model, SoilmodelFactory::create());

        $json = json_encode($rchPackage);
        $this->assertJson($json);
        $riverPackageData = json_decode($json);
        $this->assertObjectHasAttribute('nrchop', $riverPackageData);
        $this->assertObjectHasAttribute('ipakcb', $riverPackageData);
        $this->assertObjectHasAttribute('rech', $riverPackageData);

        $rech = json_decode(json_encode($riverPackageData->rech), true);
        $expected = array();
        $expected[0][0][1] = 1.1;
        $expected[0][0][2] = 1.1;
        $expected[0][1][2] = 1.1;
        $expected[0][2][0] = 2.1;
        $expected[0][2][1] = 2.1;
        $expected[0][2][2] = 2.1;
        $expected[1][0][1] = 1.2;
        $expected[1][0][2] = 1.2;
        $expected[1][1][2] = 1.2;
        $expected[1][2][0] = 2.2;
        $expected[1][2][1] = 2.2;
        $expected[1][2][2] = 2.2;

        $this->assertEquals(
            $expected,
            $rech
        );

        $this->assertObjectHasAttribute('irch', $riverPackageData);
        $this->assertObjectHasAttribute('extension', $riverPackageData);
        $this->assertObjectHasAttribute('unitnumber', $riverPackageData);
    }

    public function tearDown() {
        unset($this->model);
    }

}