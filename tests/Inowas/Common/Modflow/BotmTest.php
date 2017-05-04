<?php

namespace Inowas\Common\Modflow;


use Inowas\Common\Exception\InvalidArrayDimensionException;

class BotmTest extends \PHPUnit_Framework_TestCase
{

    public function test_is_3D_array(): void
    {
        $arr3d = [
            [
                [1,3,4],
                [1,3,4],
                [1,3,4]
            ],
            [
                [1,3,4],
                [1,3,4],
                [1,3,4]
            ]
        ];

        $botm = Botm::from3DArray($arr3d);
        $this->assertTrue($botm->is3dArray($arr3d));
    }

    public function test_is_3D_array_fails_with_2dArray(): void
    {
        $arr2d =
            [
                [1,3,4],
                [1,3,4],
                [1,3,4]
            ];

        $this->expectException(InvalidArrayDimensionException::class);
        $botm = Botm::from3DArray($arr2d);
        $this->assertFalse($botm->is3dArray($arr2d));
    }

    public function test_repair_2_layers(): void
    {
        $arrToRepair = [
            [
                [2,2,2],
                [2,2,2],
                [2,2,2]
            ],
            [
                [1,1,1],
                [1,3,1],
                [1,1,1]
            ]
        ];

        $expected = [
            [
                [2,2,2],
                [2,3,2],
                [2,2,2]
            ],
            [
                [1,1,1],
                [1,2,1],
                [1,1,1]
            ]
        ];

        $botm = Botm::from3DArray($arrToRepair);
        $this->assertEquals($expected, $botm->toValue());
    }

    public function test_repair_3_layers(): void
    {
        $arrToRepair = [
            [
                [2,2,2],
                [2,1,2],
                [2,2,2]
            ],
            [
                [1,1,1],
                [1,2,1],
                [1,1,1]
            ],
            [
                [0,0,0],
                [0,3,0],
                [0,0,0]
            ]
        ];

        $expected = [
            [
                [2,2,2],
                [2,3,2],
                [2,2,2]
            ],
            [
                [1,1,1],
                [1,1,1],
                [1,1,1]
            ],
            [
                [0,0,0],
                [0,2,0],
                [0,0,0]
            ]
        ];

        $botm = Botm::from3DArray($arrToRepair);
        $this->assertEquals($expected, $botm->toValue());
    }
}
