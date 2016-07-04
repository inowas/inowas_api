<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\GeologicalLayerFactory;

class GeologicalLayerFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\GeologicalLayer', GeologicalLayerFactory::create());
    }

}
