<?php

namespace Tests\AppBundle\Entity;


use AppBundle\Entity\StreamBoundary;
use AppBundle\Model\ActiveCells;
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\StressPeriodFactory;
use AppBundle\Model\UserFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriodData;

class StreamBoundaryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var StreamBoundary
     */
    protected $boundary;

    public function setUp() {

        $this->boundary = StreamBoundaryFactory::create()
            ->setName('TestStreamBoundary')
            ->setOwner(UserFactory::createTestUser('StreamBoundary'))
            ->setPublic(true)
            ->setActiveCells(ActiveCells::fromArray(
                array(
                    array(false, false, false, false, false),
                    array(false, true, true, true, false),
                    array(false, false, true, false, false),
                    array(false, true, true, false, false),
                    array(false, true, false, false, false),
                    array(false, true, true, false, false)
                ))
            )
            ->addStressPeriod(StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('2.1.2015'))
                ->setDateTimeEnd(new \DateTime('9.1.2015'))
                ->setSteady(false)
                ->setStage(11.1)
                ->setCond(1111)
                ->setRbot(0.1)
            )
            ->addStressPeriod(StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('10.1.2015'))
                ->setDateTimeEnd(new \DateTime('12.1.2015'))
                ->setSteady(false)
                ->setStage(12.2)
                ->setCond(1222)
                ->setRbot(0.2)
            )
            ->addStressPeriod(StressPeriodFactory::createRiv()
                ->setDateTimeBegin(new \DateTime('13.1.2015'))
                ->setDateTimeEnd(new \DateTime('21.1.2015'))
                ->setSteady(false)
                ->setStage(13.3)
                ->setCond(1333)
                ->setRbot(0.3)
            )
        ;
    }

    public function testInstantiate(){
       $this->assertInstanceOf(StreamBoundary::class, $this->boundary);
    }

    public function testGenerateStressPeriodDataWithoutOtherBoundariesOrPreCalculation(){
        $stressPeriodData = $this->boundary->addStressPeriodData(array(), $this->boundary->getStressPeriods());

        $this->assertCount(3, $stressPeriodData);
        $this->assertCount(9, $stressPeriodData[0]);
        $this->assertCount(9, $stressPeriodData[1]);
        $this->assertCount(9, $stressPeriodData[2]);

        foreach ($stressPeriodData as $nSp => $stressPeriod){
            foreach ($stressPeriod as $nVal => $value){
                $this->assertInstanceOf(RivStressPeriodData::class, $value);
            }
        }

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 11.1, 1111, 0.1), $stressPeriodData[0][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 11.1, 1111, 0.1), $stressPeriodData[0][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 11.1, 1111, 0.1), $stressPeriodData[0][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 11.1, 1111, 0.1), $stressPeriodData[0][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 11.1, 1111, 0.1), $stressPeriodData[0][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 11.1, 1111, 0.1), $stressPeriodData[0][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 11.1, 1111, 0.1), $stressPeriodData[0][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 11.1, 1111, 0.1), $stressPeriodData[0][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 11.1, 1111, 0.1), $stressPeriodData[0][8]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 12.2, 1222, 0.2), $stressPeriodData[1][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 12.2, 1222, 0.2), $stressPeriodData[1][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 12.2, 1222, 0.2), $stressPeriodData[1][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 12.2, 1222, 0.2), $stressPeriodData[1][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 12.2, 1222, 0.2), $stressPeriodData[1][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 12.2, 1222, 0.2), $stressPeriodData[1][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 12.2, 1222, 0.2), $stressPeriodData[1][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 12.2, 1222, 0.2), $stressPeriodData[1][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 12.2, 1222, 0.2), $stressPeriodData[1][8]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 13.3, 1333, 0.3), $stressPeriodData[2][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 13.3, 1333, 0.3), $stressPeriodData[2][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 13.3, 1333, 0.3), $stressPeriodData[2][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 13.3, 1333, 0.3), $stressPeriodData[2][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 13.3, 1333, 0.3), $stressPeriodData[2][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 13.3, 1333, 0.3), $stressPeriodData[2][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 13.3, 1333, 0.3), $stressPeriodData[2][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 13.3, 1333, 0.3), $stressPeriodData[2][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 13.3, 1333, 0.3), $stressPeriodData[2][8]);
    }

    public function testGenerateStressPeriodDataWithDifferentGlobalStressPeriods(){

        $globalStressPeriods = array_merge(
            array(StressPeriodFactory::createRiv()
                    ->setDateTimeBegin(new \DateTime('1.1.2015'))
                    ->setDateTimeEnd(new \DateTime('1.1.2015'))
                    ->setSteady(true)
                    ->setStage(11.1)
                    ->setCond(1111)
                    ->setRbot(0.1)
            ), $this->boundary->getStressPeriods()->toArray());

        $stressPeriodData = $this->boundary->addStressPeriodData(array(), new ArrayCollection($globalStressPeriods));

        $this->assertCount(3, $stressPeriodData);
        $this->assertCount(9, $stressPeriodData[1]);
        $this->assertCount(9, $stressPeriodData[2]);
        $this->assertCount(9, $stressPeriodData[3]);

        foreach ($stressPeriodData as $nSp => $stressPeriod){
            foreach ($stressPeriod as $nVal => $value){
                $this->assertInstanceOf(RivStressPeriodData::class, $value);
            }
        }

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 11.1, 1111, 0.1), $stressPeriodData[1][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 11.1, 1111, 0.1), $stressPeriodData[1][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 11.1, 1111, 0.1), $stressPeriodData[1][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 11.1, 1111, 0.1), $stressPeriodData[1][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 11.1, 1111, 0.1), $stressPeriodData[1][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 11.1, 1111, 0.1), $stressPeriodData[1][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 11.1, 1111, 0.1), $stressPeriodData[1][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 11.1, 1111, 0.1), $stressPeriodData[1][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 11.1, 1111, 0.1), $stressPeriodData[1][8]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 12.2, 1222, 0.2), $stressPeriodData[2][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 12.2, 1222, 0.2), $stressPeriodData[2][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 12.2, 1222, 0.2), $stressPeriodData[2][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 12.2, 1222, 0.2), $stressPeriodData[2][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 12.2, 1222, 0.2), $stressPeriodData[2][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 12.2, 1222, 0.2), $stressPeriodData[2][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 12.2, 1222, 0.2), $stressPeriodData[2][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 12.2, 1222, 0.2), $stressPeriodData[2][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 12.2, 1222, 0.2), $stressPeriodData[2][8]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 13.3, 1333, 0.3), $stressPeriodData[3][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 13.3, 1333, 0.3), $stressPeriodData[3][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 13.3, 1333, 0.3), $stressPeriodData[3][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 13.3, 1333, 0.3), $stressPeriodData[3][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 13.3, 1333, 0.3), $stressPeriodData[3][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 13.3, 1333, 0.3), $stressPeriodData[3][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 13.3, 1333, 0.3), $stressPeriodData[3][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 13.3, 1333, 0.3), $stressPeriodData[3][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 13.3, 1333, 0.3), $stressPeriodData[3][8]);
    }

    public function testGenerateStressPeriodDataAfterOtherRiverBoundary(){

        $stressPeriodData = $this->boundary->addStressPeriodData(array(), $this->boundary->getStressPeriods());

        $this->assertCount(3, $stressPeriodData);
        $this->assertCount(9, $stressPeriodData[0]);
        $this->assertCount(9, $stressPeriodData[1]);
        $this->assertCount(9, $stressPeriodData[2]);

        $this->boundary->setActiveCells(ActiveCells::fromArray(
            array(
                array(true, false, false, false, false),
                array(false, false, false, false, false),
                array(false, false, false, false, false),
                array(false, false, false, false, false),
                array(false, false, false, false, false),
                array(false, false, false, false, false)
            ))
        );

        $stressPeriodData = $this->boundary->addStressPeriodData($stressPeriodData, $this->boundary->getStressPeriods());

        $this->assertCount(3, $stressPeriodData);
        $this->assertCount(10, $stressPeriodData[0]);
        $this->assertCount(10, $stressPeriodData[1]);
        $this->assertCount(10, $stressPeriodData[2]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 11.1, 1111, 0.1), $stressPeriodData[0][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 11.1, 1111, 0.1), $stressPeriodData[0][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 11.1, 1111, 0.1), $stressPeriodData[0][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 11.1, 1111, 0.1), $stressPeriodData[0][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 11.1, 1111, 0.1), $stressPeriodData[0][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 11.1, 1111, 0.1), $stressPeriodData[0][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 11.1, 1111, 0.1), $stressPeriodData[0][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 11.1, 1111, 0.1), $stressPeriodData[0][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 11.1, 1111, 0.1), $stressPeriodData[0][8]);
        $this->assertEquals(RivStressPeriodData::create(0, 0, 0, 11.1, 1111, 0.1), $stressPeriodData[0][9]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 12.2, 1222, 0.2), $stressPeriodData[1][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 12.2, 1222, 0.2), $stressPeriodData[1][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 12.2, 1222, 0.2), $stressPeriodData[1][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 12.2, 1222, 0.2), $stressPeriodData[1][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 12.2, 1222, 0.2), $stressPeriodData[1][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 12.2, 1222, 0.2), $stressPeriodData[1][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 12.2, 1222, 0.2), $stressPeriodData[1][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 12.2, 1222, 0.2), $stressPeriodData[1][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 12.2, 1222, 0.2), $stressPeriodData[1][8]);
        $this->assertEquals(RivStressPeriodData::create(0, 0, 0, 12.2, 1222, 0.2), $stressPeriodData[1][9]);

        $this->assertEquals(RivStressPeriodData::create(0, 1, 1, 13.3, 1333, 0.3), $stressPeriodData[2][0]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 2, 13.3, 1333, 0.3), $stressPeriodData[2][1]);
        $this->assertEquals(RivStressPeriodData::create(0, 1, 3, 13.3, 1333, 0.3), $stressPeriodData[2][2]);
        $this->assertEquals(RivStressPeriodData::create(0, 2, 2, 13.3, 1333, 0.3), $stressPeriodData[2][3]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 1, 13.3, 1333, 0.3), $stressPeriodData[2][4]);
        $this->assertEquals(RivStressPeriodData::create(0, 3, 2, 13.3, 1333, 0.3), $stressPeriodData[2][5]);
        $this->assertEquals(RivStressPeriodData::create(0, 4, 1, 13.3, 1333, 0.3), $stressPeriodData[2][6]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 1, 13.3, 1333, 0.3), $stressPeriodData[2][7]);
        $this->assertEquals(RivStressPeriodData::create(0, 5, 2, 13.3, 1333, 0.3), $stressPeriodData[2][8]);
        $this->assertEquals(RivStressPeriodData::create(0, 0, 0, 13.3, 1333, 0.3), $stressPeriodData[2][9]);
    }
}
