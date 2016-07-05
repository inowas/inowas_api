<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\GeologicalUnitFactory;

class GeologicalUnitFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\GeologicalUnit', GeologicalUnitFactory::create());
    }

}
