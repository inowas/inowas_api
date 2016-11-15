<?php

namespace Inowas\Soilmodel\Tests\Model;

use Inowas\Soilmodel\Exception\InvalidArgumentException;
use Inowas\Soilmodel\Model\PropertyValue;

class LayerPropertyValueTest extends \PHPUnit_Framework_TestCase
{

    /** @var array */
    private $values2D;

    public function setUp(){

        $this->values2D = array(
            [1,3,4],
            [1,3,4],
            [1,3,4]
        );
    }

    public function testCreateFrom2DArray(){
        $soilModelPropertyValue = PropertyValue::fromValue($this->values2D);
        $this->assertInstanceOf(PropertyValue::class, $soilModelPropertyValue);
        $this->assertEquals($this->values2D, $soilModelPropertyValue->getValue());
    }

    public function testCreateFrom1DArrayThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $value1D = $this->values2D[0];
        $this->assertInstanceOf(PropertyValue::class, PropertyValue::fromValue($value1D));
    }

    public function testCreateFrom3DArrayThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $value3D = array($this->values2D);
        $this->assertInstanceOf(PropertyValue::class, PropertyValue::fromValue($value3D));
    }

    public function tearDown(){
        unset($this->values2D);
    }
}
