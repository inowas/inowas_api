<?php

namespace Tests\AppBundle\Model;

use AppBundle\Entity\WellBoundary;
use AppBundle\Model\WellBoundaryFactory;

class WellFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\WellBoundary', WellBoundaryFactory::create());
    }

    public function testInstantiateIndustrialWell(){
        $well = WellBoundaryFactory::createIndustrialWell();
        $this->assertInstanceOf('AppBundle\Entity\WellBoundary', $well);
        $this->assertEquals(WellBoundary::TYPE_INDUSTRIAL_WELL, $well->getWellType());
    }

    public function testInstantiatePrivateWell(){
        $well = WellBoundaryFactory::createPrivateWell();
        $this->assertInstanceOf('AppBundle\Entity\WellBoundary', $well);
        $this->assertEquals(WellBoundary::TYPE_PRIVATE_WELL, $well->getWellType());
    }
    
    

}
