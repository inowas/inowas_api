<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Boundary;
use AppBundle\Entity\ModelObject;
use AppBundle\Entity\User;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ModelObjectSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var User $owner */
    protected $owner;

    /** @var ModelObject */
    protected $modelObject;
    
    /** @var Boundary */
    protected $boundary;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->owner = UserFactory::createTestUser("ModelTest_Owner")->setId(1);

        $this->boundary = BoundaryFactory::create()
            ->setName('Boundary_Name')
            ->setOwner($this->owner)
            ->addSoilModel(SoilModelFactory::create()
                ->setName('SoilModel')
                ->setPublic(true)
                ->setOwner($this->owner)
            )
            ->addProperty(PropertyFactory::create()
                ->setName('ABoundaryProperty')
                ->setPropertyType(PropertyTypeFactory::create()
                    ->setName("PropertyTypeName")
                    ->setAbbreviation("PTN")
                )
            )
            ->addObservationPoint(ObservationPointFactory::create()
                ->setName('ObservationPointName')
            )
            ->addGeologicalLayer(GeologicalLayerFactory::create()
                ->setName('GeologicalLayerName_1')
            )
            ->addGeologicalLayer(GeologicalLayerFactory::create()
                ->setName('GeologicalLayerName_2')
            )
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime())
            ->setPublic(true)
        ;
    }

    public function testModelObjectGeneralList()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectlist');
        $boundary = $this->serializer->serialize($this->boundary, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$boundary);
        $boundary = json_decode($boundary);

        $this->assertEquals($boundary->type, 'boundary');
        $this->assertEquals($boundary->id, $this->boundary->getId());
        $this->assertEquals($boundary->name, $this->boundary->getName());
        $this->assertEquals($boundary->public, $this->boundary->getPublic());
        $this->assertEquals($boundary->owner->id, $this->boundary->getOwner()->getId());
    }

    public function testModelObjectGeneralDetails()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');
        $boundary = $this->serializer->serialize($this->boundary, 'json', $serializationContext);

        $this->assertStringStartsWith('{',$boundary);
        $boundary = json_decode($boundary);

        $this->assertEquals($boundary->type, 'boundary');
        $this->assertEquals($boundary->id, $this->boundary->getId());
        $this->assertEquals($boundary->name, $this->boundary->getName());
        $this->assertEquals($boundary->owner->id, $this->boundary->getOwner()->getId());

        $this->assertCount(1, $boundary->soil_models);
        $this->assertEquals($boundary->soil_models[0]->id, $this->boundary->getSoilModels()->first()->getId());

        $this->assertEquals($boundary->owner->id, $this->boundary->getOwner()->getId());

        $this->assertCount(1, $boundary->properties);
        $this->assertEquals($boundary->properties[0]->id, $this->boundary->getProperties()->first()->getId());
        $this->assertEquals($boundary->properties[0]->name, $this->boundary->getProperties()->first()->getName());
        $this->assertEquals($boundary->properties[0]->property_type->name,
            $this->boundary->getProperties()->first()->getPropertyType()->getName());
        $this->assertEquals($boundary->properties[0]->property_type->abbreviation,
            $this->boundary->getProperties()->first()->getPropertyType()->getAbbreviation());

        $this->assertCount(1, $boundary->observation_points);
        $this->assertEquals($boundary->observation_points[0]->id, $this->boundary->getObservationPoints()->first()->getId());
        $this->assertEquals($boundary->public, $this->boundary->getPublic());
        $this->assertEquals(new \DateTime($boundary->date_created), $this->boundary->getDateCreated());
        $this->assertEquals(new \DateTime($boundary->date_modified), $this->boundary->getDateModified());

        $this->assertObjectHasAttribute('geological_layers', $boundary);
        $this->assertEquals($this->boundary->getGeologicalLayers()->count(), count($boundary->geological_layers));
        $this->assertEquals($this->boundary->getGeologicalLayers()->first()->getId(), $boundary->geological_layers[0]->id);
    }
}
