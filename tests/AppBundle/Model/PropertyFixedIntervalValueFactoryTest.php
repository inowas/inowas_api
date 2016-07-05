<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\PropertyFixedIntervalValueFactory;

class PropertyFixedIntervalValueFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\PropertyFixedIntervalValue', PropertyFixedIntervalValueFactory::create());
    }

}
