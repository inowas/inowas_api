<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalPoint;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\Point;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class GeologicalPointSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var GeologicalPoint $geologicalLayer */
    protected $geologicalPoint;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->geologicalPoint = GeologicalPointFactory::create()
            ->setId(12)
            ->setName('GeologicalLayerName')
            ->setPublic(true)
            ->setOwner(
                UserFactory::createTestUser('GeologicalLayerTestUser')
            )
            ->setPoint(new Point(11.1, 12.1, 3542))
            ->addGeologicalUnit(
                GeologicalUnitFactory::create()
                    ->setPublic(true)
                    ->setName('GeologicalUnit')
            )
        ;
    }

    public function testGeologicalPointDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $geologicalPoint = $this->serializer->serialize($this->geologicalPoint, 'json', $serializationContext);
        $this->assertStringStartsWith('{', $geologicalPoint);
        $geologicalPoint = json_decode($geologicalPoint);

        $this->assertEquals($geologicalPoint->type, 'geologicalpoint');
        $this->assertEquals($geologicalPoint->owner->id, $this->geologicalPoint->getOwner()->getId());

        $this->assertEquals($geologicalPoint->point->x, $this->geologicalPoint->getPoint()->getX());
        $this->assertEquals($geologicalPoint->point->y, $this->geologicalPoint->getPoint()->getY());
        $this->assertEquals($geologicalPoint->point->srid, $this->geologicalPoint->getPoint()->getSrid());

        $this->assertCount(1, $geologicalPoint->geological_units);
        $this->assertEquals($geologicalPoint->geological_units[0]->id, $this->geologicalPoint->getGeologicalUnits()->first()->getId());
        $this->assertEquals($geologicalPoint->geological_units[0]->name, $this->geologicalPoint->getGeologicalUnits()->first()->getName());
    }
}
