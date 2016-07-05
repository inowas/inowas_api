<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\GeologicalPointFactory;

class GeologicalPointFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\GeologicalPoint', GeologicalPointFactory::create());
    }

}
