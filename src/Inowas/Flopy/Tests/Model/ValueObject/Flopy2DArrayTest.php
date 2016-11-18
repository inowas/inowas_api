<?php

namespace Inowas\FlopyBundle\Tests\Model\ValueObject;

use Inowas\FlopyBundle\Exception\InvalidArgumentException;
use Inowas\FlopyBundle\Model\ValueObject\Flopy2DArray;

class Flopy2DArrayTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiateFromNumericWithFloatValue(){
        $f2d = Flopy2DArray::fromNumeric(1.1, 4, 5);
        $this->assertInstanceOf(Flopy2DArray::class, $f2d);
        $this->assertEquals(1.1, $f2d->toReducedArray());
        $this->assertTrue(is_array($f2d->toArray()));

        $values = $f2d->toArray();
        $this->assertCount(4, $values);

        foreach ($values as $row){
            $this->assertTrue(is_array($row));
            $this->assertCount(5, $row);

            foreach ($row as $column){
                $this->assertEquals(1.1, $column);
            }
        }

        $expectedArray = array(
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(1.1, 1.1, 1.1, 1.1, 1.1)
        );

        $this->assertEquals($expectedArray, $f2d->toArray());
    }

    public function testInstantiateFromValueWithFloatValue(){
        $f2d = Flopy2DArray::fromValue(1.1, 4, 5);
        $this->assertInstanceOf(Flopy2DArray::class, $f2d);
        $this->assertEquals(1.1, $f2d->toReducedArray());
        $this->assertTrue(is_array($f2d->toArray()));

        $values = $f2d->toArray();
        $this->assertCount(4, $values);

        foreach ($values as $row){
            $this->assertTrue(is_array($row));
            $this->assertCount(5, $row);

            foreach ($row as $column){
                $this->assertEquals(1.1, $column);
            }
        }

        $expectedArray = array(
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(1.1, 1.1, 1.1, 1.1, 1.1)
        );

        $this->assertEquals($expectedArray, $f2d->toArray());
    }

    public function testInstantiateFromNumericWithNotNumericThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        Flopy2DArray::fromNumeric('abc', 1,2);
    }

    public function testInstantiateFrom1DArrayWithFloatValues(){
    $f2d = Flopy2DArray::from1DArray(array(1.1, 2.2, 3.3, 4.4), 5);
    $this->assertInstanceOf(Flopy2DArray::class, $f2d);
    $this->assertEquals(array(1.1, 2.2, 3.3, 4.4), $f2d->toReducedArray());

    $expectedArray = array(
        array(1.1, 1.1, 1.1, 1.1, 1.1),
        array(2.2, 2.2, 2.2, 2.2, 2.2),
        array(3.3, 3.3, 3.3, 3.3, 3.3),
        array(4.4, 4.4, 4.4, 4.4, 4.4)
    );

    $this->assertEquals($expectedArray, $f2d->toArray());
}

    public function testInstantiateFromValueWith1DArrayFloatValues(){
        $f2d = Flopy2DArray::fromValue(array(1.1, 2.2, 3.3, 4.4), 5, 4);
        $this->assertInstanceOf(Flopy2DArray::class, $f2d);
        $this->assertEquals(array(1.1, 2.2, 3.3, 4.4), $f2d->toReducedArray());

        $expectedArray = array(
            array(1.1, 1.1, 1.1, 1.1, 1.1),
            array(2.2, 2.2, 2.2, 2.2, 2.2),
            array(3.3, 3.3, 3.3, 3.3, 3.3),
            array(4.4, 4.4, 4.4, 4.4, 4.4)
        );

        $this->assertEquals($expectedArray, $f2d->toArray());
    }

    public function testInstantiateFrom1DArrayWithNotNumericThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        Flopy2DArray::from1DArray(array('abc', 1,2,3),2);
    }

    public function testInstantiateFrom2DArrayWithFloatValues(){
        $f2d = Flopy2DArray::from2DArray(array(
                array(1.1, 2.2, 3.3, 4.4),
                array(1.2, 2.3, 3.4, 4.5),
                array(1.3, 2.4, 3.5, 4.6),
                array(1.4, 2.5, 3.6, 4.7),
                array(1.5, 2.6, 3.7, 4.8)
            ));
        $this->assertInstanceOf(Flopy2DArray::class, $f2d);

        $expectedArray = array(
            array(1.1, 2.2, 3.3, 4.4),
            array(1.2, 2.3, 3.4, 4.5),
            array(1.3, 2.4, 3.5, 4.6),
            array(1.4, 2.5, 3.6, 4.7),
            array(1.5, 2.6, 3.7, 4.8)
        );

        $this->assertEquals($expectedArray, $f2d->toReducedArray());
        $this->assertEquals($expectedArray, $f2d->toArray());
    }

    public function testInstantiateFromValueWith2DArrayFloatValues(){
        $f2d = Flopy2DArray::fromValue(array(
            array(1.1, 2.2, 3.3, 4.4),
            array(1.2, 2.3, 3.4, 4.5),
            array(1.3, 2.4, 3.5, 4.6),
            array(1.4, 2.5, 3.6, 4.7),
            array(1.5, 2.6, 3.7, 4.8)
        ), 5, 4);
        $this->assertInstanceOf(Flopy2DArray::class, $f2d);

        $expectedArray = array(
            array(1.1, 2.2, 3.3, 4.4),
            array(1.2, 2.3, 3.4, 4.5),
            array(1.3, 2.4, 3.5, 4.6),
            array(1.4, 2.5, 3.6, 4.7),
            array(1.5, 2.6, 3.7, 4.8)
        );

        $this->assertEquals($expectedArray, $f2d->toReducedArray());
        $this->assertEquals($expectedArray, $f2d->toArray());
    }

    public function testInstantiateFrom2DArrayWithNotNumericThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        Flopy2DArray::from2DArray(array(
            array('abc', 2.2, 3.3, 4.4),
            array(1.2, 2.3, 3.4, 4.5),
            array(1.3, 2.4, 3.5, 4.6),
            array(1.4, 2.5, 3.6, 4.7),
            array(1.5, 2.6, 3.7, 4.8)
        ));
    }
}
