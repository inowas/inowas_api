<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTypeFactory;

class PropertyFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\Property', PropertyFactory::create());
    }

    public function testCreateWithType(){
        $this->assertInstanceOf('AppBundle\Entity\Property', PropertyFactory::createWithType(PropertyTypeFactory::create()));
    }

}
