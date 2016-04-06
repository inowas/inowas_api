<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\Point;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class GeologicalUnitSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var GeologicalUnit $geologicalUnit */
    protected $geologicalUnit;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->geologicalUnit = GeologicalUnitFactory::create()
            ->setId(12)
            ->setName('GeologicalUnitName')
            ->setPublic(true)
            ->setOwner(
                UserFactory::createTestUser('GeologicalUnitTestUser')
                ->setId(11)
            )
            ->setTopElevation(13.1)
            ->setBottomElevation(12.1)
            ->setGeologicalPoint(
                GeologicalPointFactory::create()
                    ->setId(21)
                    ->setPublic(true)
                    ->setPoint(new Point(11.1, 12.1, 3542))
            )
            ->addGeologicalLayer(
                GeologicalLayerFactory::create()
                ->setId(22)
                ->setPublic(true)
            )
        ;
    }

    public function testGeologicalUnitDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $geologicalUnit = $this->serializer->serialize($this->geologicalUnit, 'json', $serializationContext);
        $this->assertStringStartsWith('{', $geologicalUnit);
        $geologicalUnit = json_decode($geologicalUnit);

        $this->assertEquals($geologicalUnit->type, 'geologicalunit');
        $this->assertEquals($geologicalUnit->owner->id, $this->geologicalUnit->getOwner()->getId());

        $this->assertEquals($geologicalUnit->top_elevation, $this->geologicalUnit->getTopElevation());
        $this->assertEquals($geologicalUnit->bottom_elevation, $this->geologicalUnit->getBottomElevation());
        
        $this->assertEquals($geologicalUnit->geological_point->id, $this->geologicalUnit->getGeologicalPoint()->getId());
        $this->assertEquals($geologicalUnit->geological_point->point->x, $this->geologicalUnit->getGeologicalPoint()->getPoint()->getX());
        $this->assertEquals($geologicalUnit->geological_point->point->y, $this->geologicalUnit->getGeologicalPoint()->getPoint()->getY());
        $this->assertEquals($geologicalUnit->geological_point->point->srid, $this->geologicalUnit->getGeologicalPoint()->getPoint()->getSrid());
    }
}
