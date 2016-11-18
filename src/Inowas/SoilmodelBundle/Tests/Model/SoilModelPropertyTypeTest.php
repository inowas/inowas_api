<?php

namespace Inowas\Soilmodel\Tests\Model;

use Inowas\SoilmodelBundle\Exception\InvalidArgumentException;
use Inowas\SoilmodelBundle\Model\PropertyType;

class SoilmodelPropertyTypeTest extends \PHPUnit_Framework_TestCase
{
    public function setUp(){}

    public function testInstantiateWithKnownTypeReturnsSoilmodelPropertyType(){
        $this->assertInstanceOf(PropertyType::class, PropertyType::fromString('kx'));
    }

    public function testInstantiateWithUnknownTypeThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->assertInstanceOf(PropertyType::class, PropertyType::fromString('foo'));
    }
}
