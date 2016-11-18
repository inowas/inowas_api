<?php

namespace Inowas\FlopyBundle\Tests\Model\ValueObject;

use Inowas\FlopyBundle\Model\ValueObject\IBound;
use Inowas\ModflowBundle\Model\ActiveCells;

class IBoundTest extends \PHPUnit_Framework_TestCase
{
    private $activeCells;

    public function setUp(){
        $activeCellsStub = $this->getMockBuilder(ActiveCells::class)
            ->disableOriginalConstructor()
            ->getMock()
        ;

        $this->activeCells = $activeCellsStub;
    }

    public function testInstantiate(){

        $this->activeCells->method('toArray')
            ->willReturn(array(
                array(1,0,1),
                array(1,1,0),
            ));

        $iBound = IBound::fromActiveCells($this->activeCells, 1, 2, 3);
        $this->assertInstanceOf(IBound::class, $iBound);
    }

    public function testToArrayWithPartArray(){

        $cells[0][1] = 1;
        $cells[0][2] = 1;
        $cells[1][1] = 1;

        $this->activeCells->method('toArray')
            ->willReturn($cells);

        $expected = array(
            array(
                array(0,1,1),
                array(0,1,0)
            )
        );

        /** @var ActiveCells $activeCellsStub */
        $iBound = IBound::fromActiveCells($this->activeCells, 1, 2, 3);
        $this->assertInstanceOf(IBound::class, $iBound);
        $this->assertEquals($expected, $iBound->toArray());
    }

    public function testToArrayWithFullArray(){

        $this->activeCells->method('toArray')
            ->willReturn(array(
                array(1,0,1),
                array(1,1,0),
            ));

        $expected = array(
            array(
                array(1,0,1),
                array(1,1,0)
            )
        );

        /** @var ActiveCells $activeCellsStub */
        $iBound = IBound::fromActiveCells($this->activeCells, 1, 2, 3);
        $this->assertInstanceOf(IBound::class, $iBound);
        $this->assertEquals($expected, $iBound->toArray());
    }

    public function testJsonSerialize(){

        $this->activeCells->method('toArray')
            ->willReturn(array(
                array(1,0,1),
                array(1,1,0),
            ));

        $iBound = IBound::fromActiveCells($this->activeCells, 1, 2, 3);
        $this->assertJson(json_encode($iBound));
        $this->assertEquals("[[[1,0,1],[1,1,0]]]", json_encode($iBound));
    }

}
