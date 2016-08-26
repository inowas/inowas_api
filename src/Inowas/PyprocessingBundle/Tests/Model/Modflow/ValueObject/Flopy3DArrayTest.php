<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Modflow\ValueObject;

use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

class Flopy3DArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateFromNumeric(){
        $f3d = Flopy3DArray::fromNumeric(1.1, 2, 3, 4);
        $this->assertInstanceOf(Flopy3DArray::class, $f3d);
    }

    public function testGetReducedArrayFromNumericValue(){
        $f3d = Flopy3DArray::fromNumeric(1.1, 2, 3, 4);
        $this->assertEquals(1.1, $f3d->toReducedArray());
    }

    public function testGetReducedArrayFromValue(){
        $f3d = Flopy3DArray::fromValue(1.1, 2, 3, 4);
        $this->assertEquals(1.1, $f3d->toReducedArray());
    }

    public function testGetArrayFromNumericValue(){
        $f3d = Flopy3DArray::fromNumeric(1.1, 2, 4, 5);
        $this->assertEquals(1.1, $f3d->toReducedArray());
        $this->assertTrue(is_array($f3d->toArray()));

        $expectedArray = array(
            array(
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1)
            ),
            array(
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1)
            )
        );

        $this->assertEquals($expectedArray, $f3d->toArray());
    }

    public function testFromValueWithNumericValue(){
        $f3d = Flopy3DArray::fromValue(1.1, 2, 4, 5);
        $this->assertEquals(1.1, $f3d->toReducedArray());
        $this->assertTrue(is_array($f3d->toArray()));

        $expectedArray = array(
            array(
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1)
            ),
            array(
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1),
                array(1.1, 1.1, 1.1, 1.1, 1.1)
            )
        );

        $this->assertEquals($expectedArray, $f3d->toArray());
    }

    public function testInstantiateFrom1DArray(){
        $arr1D = array(1,2);
        $f3d = Flopy3DArray::from1DArray($arr1D, 3, 4);
        $this->assertInstanceOf(Flopy3DArray::class, $f3d);
    }

    public function testGetReducedArrayFrom1DArray()
    {
        $arr1D = array(1, 2);
        $f3d = Flopy3DArray::from1DArray($arr1D, 3, 4);
        $this->assertEquals($arr1D, $f3d->toReducedArray());

        $arr1D = array(3, 3);
        $f3d = Flopy3DArray::from1DArray($arr1D, 3, 4);
        $this->assertEquals(3, $f3d->toReducedArray());
    }

    public function testFromValueWith1DArray(){
        $arr1D = array(1,2);
        $f3d = Flopy3DArray::fromValue($arr1D, 2, 3, 4);
        $this->assertEquals($arr1D, $f3d->toReducedArray());

        $arr1D = array(3,3);
        $f3d = Flopy3DArray::fromValue($arr1D, 2, 3, 4);
        $this->assertEquals(3, $f3d->toReducedArray());
    }

    public function testGetArrayFrom1DArray(){
        $arr1D = array(1,2);
        $f3d = Flopy3DArray::from1DArray($arr1D, 3, 4);

        $expected = array(
            array(
                array(1,1,1,1),
                array(1,1,1,1),
                array(1,1,1,1)
            ),
            array(
                array(2,2,2,2),
                array(2,2,2,2),
                array(2,2,2,2)
            )
        );

        $this->assertEquals($expected, $f3d->toArray());
    }

    public function testInstantiateFrom2DArray(){
        $arr2d = array(
            array(1,2),
            array(2,3)
        );

        $f3d = Flopy3DArray::from2DArray($arr2d, 5);
        $this->assertInstanceOf(Flopy3DArray::class, $f3d);
    }

    public function testGetReducedArrayFrom2DArray(){
        $arr2d = array(
            array(1,1),
            array(2,2)
        );

        $f3d = Flopy3DArray::from2DArray($arr2d, 3);
        $expected = array(1,2);
        $this->assertEquals($expected, $f3d->toReducedArray());

        $arr2d = array(
            array(2,2),
            array(2,2)
        );

        $f3d = Flopy3DArray::from2DArray($arr2d, 3);
        $expected = 2;
        $this->assertEquals($expected, $f3d->toReducedArray());
    }

    public function testFromValueWith2DArray(){
        $arr2d = array(
            array(1,1,1),
            array(2,2,2)
        );

        $f3d = Flopy3DArray::fromValue($arr2d, 3, 2, 2);
        $expected = array(1,2);
        $this->assertEquals($expected, $f3d->toReducedArray());

        $arr2d = array(
            array(2,2,2),
            array(2,2,2)
        );

        $f3d = Flopy3DArray::fromValue($arr2d, 3, 2, 2);
        $expected = 2;
        $this->assertEquals($expected, $f3d->toReducedArray());
    }

    public function testGetArrayFrom2DArray(){
        $arr2d = array(
            array(1,2),
            array(3,4)
        );

        $f3d = Flopy3DArray::from2DArray($arr2d, 4);

        $expected = array(
            array(
                array(1,1,1,1),
                array(2,2,2,2)
            ),
            array(
                array(3,3,3,3),
                array(4,4,4,4)
            )
        );

        $this->assertEquals($expected, $f3d->toArray());
    }

    public function testInstantiateFrom3DArray(){

        $arr3d = array(
            array(array(1,2), array(2,3)),
            array(array(1,2), array(2,3))
        );

        $f3d = Flopy3DArray::from3DArray($arr3d);
        $this->assertInstanceOf(Flopy3DArray::class, $f3d);
    }

    public function testGetReducedArrayFrom3DArray(){
        $arr3d =
            array(
                array(
                    array(1,2),
                    array(3,4)
                ),
                array(
                    array(2,3),
                    array(4,5)
                )
            )
        ;

        $f3d = Flopy3DArray::from3DArray($arr3d);
        $this->assertEquals($arr3d, $f3d->toReducedArray());


        $arr3d =
            array(
                array(
                    array(1,1),
                    array(2,2)
                ),
                array(
                    array(3,3),
                    array(5,5)
                )
            )
        ;

        $expected =
            array(
                array(1,2),
                array(3,5)
            )
        ;

        $f3d = Flopy3DArray::from3DArray($arr3d);
        $this->assertEquals($expected, $f3d->toReducedArray());



        $arr3d =
            array(
                array(
                    array(2,2),
                    array(2,2)
                ),
                array(
                    array(3,3),
                    array(3,3)
                )
            )
        ;

        $expected = array(2,3);

        $f3d = Flopy3DArray::from3DArray($arr3d);
        $this->assertEquals($expected, $f3d->toReducedArray());

        $arr3d =
            array(
                array(
                    array(2,2),
                    array(2,2)
                ),
                array(
                    array(2,2),
                    array(2,2)
                )
            )
        ;

        $expected = 2;
        $f3d = Flopy3DArray::from3DArray($arr3d);
        $this->assertEquals($expected, $f3d->toReducedArray());

    }

    public function testFromValueWith3DArray(){
        $arr3d =
            array(
                array(
                    array(1,2),
                    array(3,4)
                ),
                array(
                    array(2,3),
                    array(4,5)
                )
            )
        ;

        $f3d = Flopy3DArray::fromValue($arr3d, 2, 2, 2);
        $this->assertEquals($arr3d, $f3d->toReducedArray());


        $arr3d =
            array(
                array(
                    array(1,1),
                    array(2,2)
                ),
                array(
                    array(3,3),
                    array(5,5)
                )
            )
        ;

        $expected =
            array(
                array(1,2),
                array(3,5)
            )
        ;

        $f3d = Flopy3DArray::fromValue($arr3d, 2, 2, 2);
        $this->assertEquals($expected, $f3d->toReducedArray());



        $arr3d =
            array(
                array(
                    array(2,2),
                    array(2,2)
                ),
                array(
                    array(3,3),
                    array(3,3)
                )
            )
        ;

        $expected = array(2,3);

        $f3d = Flopy3DArray::fromValue($arr3d, 2, 2, 2);
        $this->assertEquals($expected, $f3d->toReducedArray());

        $arr3d =
            array(
                array(
                    array(2,2),
                    array(2,2)
                ),
                array(
                    array(2,2),
                    array(2,2)
                )
            )
        ;

        $expected = 2;
        $f3d = Flopy3DArray::fromValue($arr3d, 2, 2, 2);
        $this->assertEquals($expected, $f3d->toReducedArray());
    }

    public function testJsonSerialize(){
        $f3d = Flopy3DArray::fromNumeric(1.1, 2, 3, 4);
        $this->assertEquals($f3d->toReducedArray(), json_decode(json_encode($f3d)));
    }
}
