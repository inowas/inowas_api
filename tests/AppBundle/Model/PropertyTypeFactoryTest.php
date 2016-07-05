<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\PropertyTypeFactory;

class PropertyTypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\PropertyType', PropertyTypeFactory::create());
    }

}
