<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalUnit;
use AppBundle\Model\GeologicalUnitFactory;
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
            ->setName('GeologicalUnitName')
            ->setPublic(true)
            ->setOwner(
                UserFactory::createTestUser('GeologicalUnitTestUser')
            )
            ->setTopElevation(13.1)
            ->setBottomElevation(12.1)
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
    }
}