<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\ConstantHeadBoundaryFactory;

class ConstantHeadBoundaryFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate()
    {
        $this->assertInstanceOf('AppBundle\Entity\ConstantHeadBoundary', ConstantHeadBoundaryFactory::create());
    }

}
