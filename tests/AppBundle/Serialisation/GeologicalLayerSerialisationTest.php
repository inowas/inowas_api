<?php

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class GeologicalLayerSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var GeologicalLayer $geologicalLayer */
    protected $geologicalLayer;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->geologicalLayer = GeologicalLayerFactory::create()
            ->setName('GeologicalLayerName')
            ->setPublic(true)
            ->setOwner(
                UserFactory::createTestUser('GeologicalLayerTestUser')
            )
            ->addGeologicalUnit(
                GeologicalUnitFactory::create()
                ->setPublic(true)
                ->setName('GeologicalUnit')
            )
        ;
    }

    public function testGeologicalLayerDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $geologicalLayer = $this->serializer->serialize($this->geologicalLayer, 'json', $serializationContext);
        $this->assertStringStartsWith('{', $geologicalLayer);
        $geologicalLayer = json_decode($geologicalLayer);
        $this->assertEquals($geologicalLayer->type, 'geologicallayer');
        $this->assertEquals($geologicalLayer->owner->id, $this->geologicalLayer->getOwner()->getId());

        $this->assertCount(1, $geologicalLayer->geological_units);
        $this->assertEquals($geologicalLayer->geological_units[0]->id, $this->geologicalLayer->getGeologicalUnits()->first()->getId());
        $this->assertEquals($geologicalLayer->geological_units[0]->name, $this->geologicalLayer->getGeologicalUnits()->first()->getName());
    }
}
