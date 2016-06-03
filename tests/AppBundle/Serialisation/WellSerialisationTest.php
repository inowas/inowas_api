<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Well;
use AppBundle\Model\Point;
use AppBundle\Model\WellFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class WellSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var Well $well */
    protected $well;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();
        
        $this->well = WellFactory::create()
            ->setName('WellName')
            ->setPoint(new Point(11777056.49104572273790836, 2403440.17028302047401667, 3452))
            ;
    }

    public function testWellDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $well = $this->serializer->serialize($this->well, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$well);
        $well = json_decode($well);
        $this->assertEquals($well->type, 'well');
        $this->assertObjectHasAttribute('id', $well);
        $this->assertEquals($this->well->getId(), $well->id);
        $this->assertObjectHasAttribute('name', $well);
        $this->assertEquals($this->well->getName(), $well->name);
        $this->assertObjectHasAttribute('point', $well);
        $point = $well->point;
        $this->assertEquals($this->well->getPoint()->getX(), $point->x);
        $this->assertEquals($this->well->getPoint()->getY(), $point->y);
        $this->assertEquals($this->well->getPoint()->getSrid(), $point->srid);
    }
}
