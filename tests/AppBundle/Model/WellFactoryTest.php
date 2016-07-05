<?php

namespace Tests\AppBundle\Model;


use AppBundle\Entity\Well;
use AppBundle\Model\WellFactory;

class WellFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\Well', WellFactory::create());
    }

    public function testInstantiateIndustrialWell(){
        $well = WellFactory::createIndustrialWell();
        $this->assertInstanceOf('AppBundle\Entity\Well', $well);
        $this->assertEquals(Well::TYPE_INDUSTRIAL_WELL, $well->getWellType());
    }

    public function testInstantiatePrivateWell(){
        $well = WellFactory::createPrivateWell();
        $this->assertInstanceOf('AppBundle\Entity\Well', $well);
        $this->assertEquals(Well::TYPE_PRIVATE_WELL, $well->getWellType());
    }
    
    

}
