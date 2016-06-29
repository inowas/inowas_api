<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\AreaTypeFactory;

class AreaTypeFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('AppBundle\Entity\AreaType', AreaTypeFactory::create());
    }

    public function testCreateWithName()
    {
        $areaType = AreaTypeFactory::setName("AreaType");
        $this->assertInstanceOf('AppBundle\Entity\AreaType', $areaType);
        $this->assertEquals('AreaType', $areaType->getName());
    }

}
