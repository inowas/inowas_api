<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\StreamBoundaryFactory;

class StreamBoundaryFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\StreamBoundary', StreamBoundaryFactory::create());
    }

}
