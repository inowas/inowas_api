<?php


namespace Tests\AppBundle\Model\RainbowVis;


use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use AppBundle\Model\RainbowVis\ColorGradient;

class ColorGradientTest extends \PHPUnit_Framework_TestCase
{

    /** @var  ColorGradient */
    protected $colorGradient;

    public function setUp()
    {
        $this->colorGradient = new ColorGradient();
    }

    public function testCanInstantiate(){
        $this->assertInstanceOf(ColorGradient::class, $this->colorGradient);
    }

    public function testCanSetGradientWithHexValuesColors(){
        $this->colorGradient->setGradient('000000', 'FFFFFF');
    }

    public function testSetGradientWithNonHexNotKnownStartValueThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->colorGradient->setGradient('123Z', 'FFFFFF');
    }

    public function testSetGradientWithNonHexNotKnownEndValueThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->colorGradient->setGradient('FFFFFF', '123Z');
    }

    public function testSetNumberRangeWithFloats(){
        $this->colorGradient->setNumberRange(1.2, 1.4);
        $this->assertEquals(1.2, $this->colorGradient->getMinValue());
        $this->assertEquals(1.4, $this->colorGradient->getMaxValue());
    }

    public function testSetNumberRangeWithFloatsWhereMinBiggerThenMax(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->colorGradient->setNumberRange(1.5, 1.2);
    }

    public function testSetNumberRangeWithFloatsWhereMinEqualMax(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->colorGradient->setNumberRange(1.2, 1.2);
    }

    public function testColorAtMinEqualsStartColor(){
        $startColor = 'FFFFFF';
        $endColor = '000000';
        $startValue = 10;
        $endValue = 20;

        $this->colorGradient->setGradient($startColor, $endColor);
        $this->colorGradient->setNumberRange($startValue, $endValue);
        $this->assertEquals('ffffff', $this->colorGradient->colorAt($startValue));
    }

    public function testColorSmallerThenMinEqualsStartColor(){
        $startColor = 'FFFFFF';
        $endColor = '000000';
        $startValue = 10;
        $endValue = 20;

        $this->colorGradient->setGradient($startColor, $endColor);
        $this->colorGradient->setNumberRange($startValue, $endValue);
        $this->assertEquals('ffffff', $this->colorGradient->colorAt($startValue-1));
    }

    public function testColorAtMaxEqualsEndColor(){
        $startColor = 'FFFFFF';
        $endColor = '000000';
        $startValue = 10;
        $endValue = 20;

        $this->colorGradient->setGradient($startColor, $endColor);
        $this->colorGradient->setNumberRange($startValue, $endValue);
        $this->assertEquals('000000', $this->colorGradient->colorAt($endValue));
    }

    public function testColorBiggerThenMaxEqualsEndColor(){
        $startColor = 'FFFFFF';
        $endColor = '000000';
        $startValue = 10;
        $endValue = 20;

        $this->colorGradient->setGradient($startColor, $endColor);
        $this->colorGradient->setNumberRange($startValue, $endValue);
        $this->assertEquals('000000', $this->colorGradient->colorAt($endValue+10));
    }

    public function testColorAt(){
        $startColor = 'FFFFFF';
        $endColor = '000000';
        $startValue = 10;
        $endValue = 20;

        $this->colorGradient->setGradient($startColor, $endColor);
        $this->colorGradient->setNumberRange($startValue, $endValue);
        $this->assertEquals('808080', $this->colorGradient->colorAt(($startValue+$endValue)/2));
    }
}
