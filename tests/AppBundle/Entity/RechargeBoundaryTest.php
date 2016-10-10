<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\RechargeBoundary;
use AppBundle\Model\RechargeBoundaryFactory;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;

class RechargeBoundaryTest extends \PHPUnit_Framework_TestCase
{
    /** @var  RechargeBoundary */
    protected $rechargeBoundary;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->rechargeBoundary = RechargeBoundaryFactory::create();
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\RechargeBoundary', $this->rechargeBoundary);
        $this->assertInstanceOf('Ramsey\Uuid\Uuid', $this->rechargeBoundary->getId());
    }

    public function testSetGetGeometry(){
        $polygon = new Polygon(array(
            array(
                array(1,2),
                array(1,3),
                array(2,3),
                array(1,2),
            )
        ));
        $this->rechargeBoundary->setGeometry($polygon);
        $this->assertEquals($polygon, $this->rechargeBoundary->getGeometry());
        $this->assertCount(1, $this->rechargeBoundary->serializeDeserializeGeometry());
        $this->assertArrayHasKey('type', $this->rechargeBoundary->serializeDeserializeGeometry()[0]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
    }
}
