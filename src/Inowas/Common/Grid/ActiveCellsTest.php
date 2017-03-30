<?php

namespace Inowas\Common\Grid;

class ActiveCellsTest extends \PHPUnit_Framework_TestCase
{

    public function test_from_full_array_and_gridsize() {
        $arr = [
            [0,2,3],
            [4,0,6],
            [5,0,4],
            [6,9,0],
        ];

        $expectedArr = [
            [0,1,1],
            [1,0,1],
            [1,0,1],
            [1,1,0],
        ];

        $gridSize = GridSize::fromXY(3, 4);
        $activeCells = ActiveCells::fromArrayAndGridSize($arr, $gridSize);
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($expectedArr, $activeCells->cells());
        $this->assertEquals($expectedArr, $activeCells->fullArray());
    }

    public function test_from_not_full_array_and_gridsize() {
        $arr = [];
        $arr[0][0] = 1;
        $arr[1][1] = 1;
        $arr[2][2] = 1;
        $arr[3][2] = 1;

        $fullArr = [
            [1,0,0],
            [0,1,0],
            [0,0,1],
            [0,0,1]
        ];

        $gridSize = GridSize::fromXY(3, 4);
        $activeCells = ActiveCells::fromArrayAndGridSize($arr, $gridSize);
        $this->assertInstanceOf(ActiveCells::class, $activeCells);
        $this->assertEquals($arr, $activeCells->cells());
        $this->assertEquals($fullArr, $activeCells->fullArray());
    }
}
