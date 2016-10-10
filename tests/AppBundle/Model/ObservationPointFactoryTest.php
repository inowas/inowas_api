<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\ObservationPointFactory;

class ObservationPointFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\ObservationPoint', ObservationPointFactory::create());
    }
}
