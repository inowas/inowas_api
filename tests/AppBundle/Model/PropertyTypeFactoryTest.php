<?php

namespace Tests\AppBundle\Model;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;

class PropertyTypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreatePropertyTypeWithCorrectAbbreviation(){
        $this->assertInstanceOf(PropertyType::class, PropertyTypeFactory::create(PropertyType::KX));
    }

    public function testCreatePropertyTypeWithNotCorrectAbbreviationThrowsException(){
        $this->setExpectedException(InvalidArgumentException::class);
        $this->assertInstanceOf(PropertyType::class, PropertyTypeFactory::create('foo'));
    }

}
