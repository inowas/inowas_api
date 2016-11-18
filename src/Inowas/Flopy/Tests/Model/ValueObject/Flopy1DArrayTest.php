<?php

namespace Inowas\FlopyBundle\Tests\Model\ValueObject;

use Inowas\FlopyBundle\Exception\InvalidArgumentException;
use Inowas\FlopyBundle\Model\ValueObject\Flopy1DArray;

class Flopy1DArrayTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiateFromNumericWithIntegerValue(){
        $f1d = Flopy1DArray::fromNumeric(1.1 ,10);
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(1.1, $f1d->toReducedArray());
        $this->assertTrue(is_array($f1d->toArray()));
        $this->assertCount(10, $f1d->toArray());

        $values = $f1d->toArray();
        foreach ($values as $value){
            $this->assertEquals(1.1, $value);
        }
    }

    public function testInstantiateFromNumericWithFloatValue(){
        $f1d = Flopy1DArray::fromNumeric(1.9, 10);
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(1.9, $f1d->toReducedArray());
    }

    public function testInstantiateFromValueWithFloatValue(){
        $f1d = Flopy1DArray::fromValue(1.9, 10);
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(1.9, $f1d->toReducedArray());
    }

    public function testInstantiateFromNumericWithNotNumericThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        Flopy1DArray::fromNumeric('abc', 1);
    }

    public function testInstantiateFromBoolWithBoolValue(){
        $f1d = Flopy1DArray::fromBool(true, 10);
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(true, $f1d->toReducedArray());
    }

    public function testInstantiateFromArray(){
        $f1d = Flopy1DArray::fromArray(array(0.1,1,2,3,4.1,5.2,6.3,7,8,9));
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(array(0.1,1,2,3,4.1,5.2,6.3,7,8,9), $f1d->toReducedArray());
    }

    public function testInstantiateFromValueWithArray(){
        $f1d = Flopy1DArray::fromValue(array(0.1,1,2,3,4.1,5.2,6.3,7,8,9), 10);
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(array(0.1,1,2,3,4.1,5.2,6.3,7,8,9), $f1d->toReducedArray());
    }

    public function testInstantiateFromArrayWithEqualValuesReturnScalarValue(){
        $f1d = Flopy1DArray::fromArray(array(1,1,1,1,1,1,1,1,1,1));
        $this->assertInstanceOf(Flopy1DArray::class, $f1d);
        $this->assertEquals(1, $f1d->toReducedArray());
    }
}

