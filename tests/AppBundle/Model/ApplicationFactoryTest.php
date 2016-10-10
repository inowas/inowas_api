<?php

namespace Tests\AppBundle\Model;


use AppBundle\Model\ApplicationFactory;

class ApplicationFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testCreate()
    {
        $this->assertInstanceOf('AppBundle\Entity\Application', ApplicationFactory::create());
    }
}
