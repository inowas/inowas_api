<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\WellBoundary;
use AppBundle\Model\Point;
use AppBundle\Model\WellBoundaryFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class WellSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var WellBoundary $well */
    protected $well;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
        
        $this->well = WellBoundaryFactory::create()
            ->setName('WellName')
            ->setGeometry(new Point(11777056.49104572273790836, 2403440.17028302047401667, 3452))
            ;
    }

    public function testWellDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $well = $this->serializer->serialize($this->well, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$well);
        $well = json_decode($well);
        $this->assertEquals($well->type, 'WEL');
        $this->assertObjectHasAttribute('id', $well);
        $this->assertEquals($this->well->getId(), $well->id);
        $this->assertObjectHasAttribute('name', $well);
        $this->assertEquals($this->well->getName(), $well->name);
        $this->assertObjectHasAttribute('point', $well);
        $point = $well->point;
        $this->assertEquals($this->well->getGeometry()->getX(), $point->x);
        $this->assertEquals($this->well->getGeometry()->getY(), $point->y);
        $this->assertEquals($this->well->getGeometry()->getSrid(), $point->srid);
    }
}