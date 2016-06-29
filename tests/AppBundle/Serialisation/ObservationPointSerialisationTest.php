<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\ObservationPoint;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\Point;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ObservationPointSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var ObservationPoint $observationPoint */
    protected $observationPoint;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->observationPoint = ObservationPointFactory::create()
            ->setName('ObservationPointName')
            ->setPublic(true)
            ->setElevation(12.11)
            ->setOwner(UserFactory::createTestUser('ObservationPointTestUser'))
            ->setGeometry(new Point(11.1, 12.1, 3542))
        ;
    }

    public function testObservationPointDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $observationPoint = $this->serializer->serialize($this->observationPoint, 'json', $serializationContext);
        $this->assertStringStartsWith('{', $observationPoint);
        $observationPoint = json_decode($observationPoint);

        $this->assertEquals($observationPoint->type, 'observationPoint');
        $this->assertEquals($observationPoint->owner->id, $this->observationPoint->getOwner()->getId());

        $this->assertEquals($observationPoint->elevation, $this->observationPoint->getElevation());

        $this->assertEquals($observationPoint->point->x, $this->observationPoint->getGeometry()->getX());
        $this->assertEquals($observationPoint->point->y, $this->observationPoint->getGeometry()->getY());
        $this->assertEquals($observationPoint->point->srid, $this->observationPoint->getGeometry()->getSrid());
    }
}
