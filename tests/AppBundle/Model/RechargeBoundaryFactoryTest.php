<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\RechargeBoundaryFactory;

class RechargeBoundaryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\RechargeBoundary', RechargeBoundaryFactory::create());
    }
}
