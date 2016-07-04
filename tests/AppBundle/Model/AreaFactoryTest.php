<?php

namespace Tests\AppBundle\Model;

use AppBundle\Model\AreaFactory;

class AreaFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('AppBundle\Entity\Area', AreaFactory::create());
    }
}
