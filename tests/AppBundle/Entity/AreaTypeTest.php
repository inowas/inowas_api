<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\AreaType;
use AppBundle\Model\AreaTypeFactory;

class AreaTypeTest extends \PHPUnit_Framework_TestCase
{

    /** @var AreaType */
    protected $areaType;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->areaType = AreaTypeFactory::create();
    }


    public function testInstantiation()
    {
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->areaType->getId());
    }

    public function testSetGetName(){
        $name = "name";
        $this->areaType->setName($name);
        $this->assertEquals($name, $this->areaType->getName());
    }


    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        unset($this->areaType);
    }
}
