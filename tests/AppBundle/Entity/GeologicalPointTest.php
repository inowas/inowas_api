<?php

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\Point;

class GeologicalPointTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var GeologicalPoint $modelObject
     */
    protected $geologicalPoint;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->geologicalPoint = GeologicalPointFactory::create()
            ->setName('TestGeologicalPoint');
    }

    public function testInstantiate(){
        $this->assertInstanceOf('AppBundle\Entity\GeologicalPoint', $this->geologicalPoint);
        $this->assertInstanceOf('Doctrine\Common\Collections\ArrayCollection', $this->geologicalPoint->getGeologicalUnits());
    }

    public function testSetGetPoint()
    {
        $point = new Point(1,2,4326);
        $this->geologicalPoint->setPoint($point);
        $this->assertEquals($point, $this->geologicalPoint->getPoint());
    }

    public function testAddGetRemoveGeologicalUnits()
    {
        $geologicalUnit = GeologicalUnitFactory::create()->setName('GeologicalUnit');
        $this->assertCount(0, $this->geologicalPoint->getGeologicalUnits());
        $this->geologicalPoint->addGeologicalUnit($geologicalUnit);
        $this->assertCount(1, $this->geologicalPoint->getGeologicalUnits());
        $this->assertEquals($geologicalUnit, $this->geologicalPoint->getGeologicalUnits()->first());
        $this->geologicalPoint->addGeologicalUnit($geologicalUnit);
        $this->assertCount(1, $this->geologicalPoint->getGeologicalUnits());
        $this->geologicalPoint->removeGeologicalUnit($geologicalUnit);
        $this->assertCount(0, $this->geologicalPoint->getGeologicalUnits());
    }

    public function testPreFlushTest() {
        $point = new Point(1,2,4326);
        $geologicalUnit = GeologicalUnitFactory::create()
            ->setName('GeologicalUnit');

        $this->geologicalPoint->setPoint($point);
        $this->geologicalPoint->addGeologicalUnit($geologicalUnit);
        $this->geologicalPoint->preFlush();
        $this->assertEquals($point, $this->geologicalPoint->getGeologicalUnits()->first()->getPoint());
    }
}
