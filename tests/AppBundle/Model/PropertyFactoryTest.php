<?php

namespace Tests\AppBundle\Model;


use AppBundle\Entity\Property;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyType;
use AppBundle\Model\PropertyTypeFactory;

class PropertyFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf(Property::class, PropertyFactory::create());
    }

    public function testCreateWithType(){
        $this->assertInstanceOf(Property::class, PropertyFactory::createWithType(PropertyTypeFactory::create(PropertyType::KX)));
    }

}
