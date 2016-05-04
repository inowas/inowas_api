<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Area;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\AreaTypeFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\Point;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class SoilModelSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var SoilModel $soilModel */
    protected $soilModel;

    /** @var Area $area */
    protected $area;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setName("TestSoilModel");
        $this->soilModel->setPublic(true);
        $this->soilModel->setDescription('TestSoilModelDescription!!!');

        $owner = UserFactory::createTestUser("SoilModelTest_Owner");
        $this->soilModel->setOwner($owner);

        $this->area = AreaFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setAreaType(AreaTypeFactory::setName('SoilModelTestAreaType'))
            ->addProperty(PropertyFactory::create()->setId(14))
        ;

        $this->soilModel->setArea($this->area);

        $layer1 = GeologicalLayerFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName("SoilModel-TestLayer_1");

        $layer2 = GeologicalLayerFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName("SoilModel-TestLayer_2");
            
        $this->soilModel->addGeologicalLayer($layer1);
        $this->soilModel->addGeologicalLayer($layer2);

        $point1 = GeologicalPointFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName('SoilModel-TestPoint_1')
            ->setDateCreated(new \DateTime('2015-01-01'))
            ->setDateModified(new \DateTime('2015-01-02'))
            ->setPoint(new Point(12,11,5432))
        ;

        $point2 = GeologicalPointFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName('SoilModel-TestPoint_2')
            ->setDateCreated(new \DateTime('2015-01-01'))
            ->setDateModified(new \DateTime('2015-01-02'))
            ->setPoint(new Point(13,12,5432))
        ;

        $this->soilModel->addGeologicalPoint($point1);
        $this->soilModel->addGeologicalPoint($point2);

        $unit_1_1 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName("SoilModel-TestUnit_1_1")
            ->setDateCreated(new \DateTime('2015-01-01'))
            ->setDateModified(new \DateTime('2015-01-02'))
            ->setTopElevation(12)
            ->setBottomElevation(10);

        $unit_1_2 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName("SoilModel-TestUnit_1_2")
            ->setDateCreated(new \DateTime('2015-01-01'))
            ->setDateModified(new \DateTime('2015-01-02'))
            ->setTopElevation(10)
            ->setBottomElevation(8);

        $unit_2_1 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName("SoilModel-TestUnit_2_1")
            ->setDateCreated(new \DateTime('2015-01-01'))
            ->setDateModified(new \DateTime('2015-01-02'))
            ->setTopElevation(12)
            ->setBottomElevation(9);

        $unit_2_2 = GeologicalUnitFactory::create()
            ->setOwner($owner)
            ->setPublic(true)
            ->setName("SoilModel-TestUnit_2_2")
            ->setDateCreated(new \DateTime('2015-01-01'))
            ->setDateModified(new \DateTime('2015-01-02'))
            ->setTopElevation(9)
            ->setBottomElevation(6);

        $layer1->addGeologicalUnit($unit_1_1);
        $layer1->addGeologicalUnit($unit_1_2);
        $layer2->addGeologicalUnit($unit_2_1);
        $layer2->addGeologicalUnit($unit_2_2);

        $point1->addGeologicalUnit($unit_1_1);
        $point1->addGeologicalUnit($unit_1_2);
        $point2->addGeologicalUnit($unit_2_1);
        $point2->addGeologicalUnit($unit_2_2);

        $this->soilModel->addGeologicalUnit($unit_1_1);
        $this->soilModel->addGeologicalUnit($unit_1_2);
        $this->soilModel->addGeologicalUnit($unit_2_1);
        $this->soilModel->addGeologicalUnit($unit_2_2);
    }

    public function testSoilModelListSerialisation()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('soilmodellist');

        $serializedSoilModel = $this->serializer->serialize($this->soilModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedSoilModel);
    }

    public function testSoilModelDetailsSerialisation()
    {   
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('soilmodeldetails');

        $serializedSoilModel = $this->serializer->serialize($this->soilModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedSoilModel);
        $serializedSoilModel = json_decode($serializedSoilModel);

        $this->assertObjectHasAttribute('id', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getId(), $serializedSoilModel->id);
        $this->assertObjectHasAttribute('name', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getName(), $serializedSoilModel->name);
        $this->assertObjectHasAttribute('description', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getDescription(), $serializedSoilModel->description);
        $this->assertObjectHasAttribute('owner', $serializedSoilModel);
        $this->assertObjectHasAttribute('id', $serializedSoilModel->owner);
        $this->assertEquals($this->soilModel->getOwner()->getId(), $serializedSoilModel->owner->id);
        $this->assertObjectHasAttribute('public', $serializedSoilModel);
        $this->assertEquals(true, $serializedSoilModel->public);
        $this->assertObjectHasAttribute('date_created', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getDateCreated(), new \DateTime($serializedSoilModel->date_created));
        $this->assertObjectHasAttribute('date_modified', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getDateModified(), new \DateTime($serializedSoilModel->date_modified));
        $this->assertObjectHasAttribute('geological_layers', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->count(), count($serializedSoilModel->geological_layers));
        $this->assertObjectHasAttribute('id', $serializedSoilModel->geological_layers[0]);
        $this->assertObjectHasAttribute('properties', $serializedSoilModel->geological_layers[0]);
        $this->assertObjectHasAttribute('observation_points', $serializedSoilModel->geological_layers[0]);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getId(), $serializedSoilModel->geological_layers[0]->id);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getName(), $serializedSoilModel->geological_layers[0]->name);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getPublic(), $serializedSoilModel->geological_layers[0]->public);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getDateCreated(), new \DateTime($serializedSoilModel->geological_layers[0]->date_created));
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getDateModified(), new \DateTime($serializedSoilModel->geological_layers[0]->date_modified));

        $this->assertObjectHasAttribute('geological_units', $serializedSoilModel->geological_layers[0]);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getGeologicalUnits()->count(), count($serializedSoilModel->geological_layers[0]->geological_units));
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getGeologicalUnits()->first()->getId(), $serializedSoilModel->geological_layers[0]->geological_units[0]->id);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getGeologicalUnits()->first()->getName(), $serializedSoilModel->geological_layers[0]->geological_units[0]->name);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getGeologicalUnits()->first()->getPublic(), $serializedSoilModel->geological_layers[0]->geological_units[0]->public);
        $this->assertObjectHasAttribute('properties', $serializedSoilModel->geological_layers[0]->geological_units[0]);
        $this->assertObjectHasAttribute('observation_points', $serializedSoilModel->geological_layers[0]->geological_units[0]);
        $this->assertObjectHasAttribute('date_created', $serializedSoilModel->geological_layers[0]->geological_units[0]);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getGeologicalUnits()->first()->getDateCreated(), new \DateTime($serializedSoilModel->geological_layers[0]->geological_units[0]->date_created));
        $this->assertObjectHasAttribute('date_modified', $serializedSoilModel->geological_layers[0]->geological_units[0]);
        $this->assertEquals($this->soilModel->getGeologicalLayers()->first()->getGeologicalUnits()->first()->getDateModified(), new \DateTime($serializedSoilModel->geological_layers[0]->geological_units[0]->date_modified));

        $this->assertObjectHasAttribute('geological_points', $serializedSoilModel);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->count(), count($serializedSoilModel->geological_points));
        $this->assertObjectHasAttribute('id', $serializedSoilModel->geological_points[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getId(), $serializedSoilModel->geological_points[0]->id);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getName(), $serializedSoilModel->geological_points[0]->name);
        $this->assertObjectHasAttribute('properties', $serializedSoilModel->geological_points[0]);
        $this->assertObjectHasAttribute('observation_points', $serializedSoilModel->geological_points[0]);
        $this->assertObjectHasAttribute('public', $serializedSoilModel->geological_points[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getPublic(), $serializedSoilModel->geological_points[0]->public);
        $this->assertObjectHasAttribute('date_created', $serializedSoilModel->geological_points[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getDateCreated(), new \DateTime($serializedSoilModel->geological_points[0]->date_created));
        $this->assertObjectHasAttribute('date_modified', $serializedSoilModel->geological_points[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getDateModified(), new \DateTime($serializedSoilModel->geological_points[0]->date_modified));

        $this->assertObjectHasAttribute('geological_units', $serializedSoilModel->geological_points[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getGeologicalUnits()->count(), count($serializedSoilModel->geological_points[0]->geological_units));
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getGeologicalUnits()->first()->getId(), $serializedSoilModel->geological_points[0]->geological_units[0]->id);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getGeologicalUnits()->first()->getName(), $serializedSoilModel->geological_points[0]->geological_units[0]->name);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getGeologicalUnits()->first()->getPublic(), $serializedSoilModel->geological_points[0]->geological_units[0]->public);
        $this->assertObjectHasAttribute('properties', $serializedSoilModel->geological_points[0]->geological_units[0]);
        $this->assertObjectHasAttribute('observation_points', $serializedSoilModel->geological_points[0]->geological_units[0]);
        $this->assertObjectHasAttribute('date_created', $serializedSoilModel->geological_points[0]->geological_units[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getGeologicalUnits()->first()->getDateCreated(), new \DateTime($serializedSoilModel->geological_points[0]->geological_units[0]->date_created));
        $this->assertObjectHasAttribute('date_modified', $serializedSoilModel->geological_layers[0]->geological_units[0]);
        $this->assertEquals($this->soilModel->getGeologicalPoints()->first()->getGeologicalUnits()->first()->getDateModified(), new \DateTime($serializedSoilModel->geological_points[0]->geological_units[0]->date_modified));

        $this->assertObjectHasAttribute('area', $serializedSoilModel);
        $this->assertObjectHasAttribute('id', $serializedSoilModel->area);
        $this->assertEquals($this->soilModel->getArea()->getId(), $serializedSoilModel->area->id);
        $this->assertObjectHasAttribute('properties', $serializedSoilModel->area);
        $this->assertEquals($this->soilModel->getArea()->getProperties()->count(), count($serializedSoilModel->area->properties));
        $this->assertObjectHasAttribute('id', $serializedSoilModel->area->properties[0]);
        $this->assertObjectHasAttribute('observation_points', $serializedSoilModel->area);
        $this->assertObjectHasAttribute('public', $serializedSoilModel->area);
        $this->assertEquals($this->soilModel->getArea()->getPublic(), $serializedSoilModel->area->public);
        $this->assertObjectHasAttribute('date_created', $serializedSoilModel->area);
        $this->assertEquals($this->soilModel->getArea()->getDateCreated(), new \DateTime($serializedSoilModel->area->date_created));
        $this->assertObjectHasAttribute('date_modified', $serializedSoilModel->area);
        $this->assertEquals($this->soilModel->getArea()->getDateModified(), new \DateTime($serializedSoilModel->area->date_modified));
    }
}
