<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\UserFactory;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use JMS\Serializer\SerializerBuilder;

class ModFlowModelSerialisationTest extends \PHPUnit_Framework_TestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var ModFlowModel $modFlowModel */
    protected $modFlowModel;

    /** @var  SoilModel */
    protected $soilModel;

    /** @var  GeologicalLayer */
    protected $layer;

    /** @var  Property */
    protected $property;

    public function setUp()
    {
        $this->serializer = SerializerBuilder::create()->build();

        $this->modFlowModel = ModFlowModelFactory::create();
        $this->modFlowModel->setId(11);
        $this->modFlowModel->setName("TestModel");
        $this->modFlowModel->setPublic(true);
        $this->modFlowModel->setDescription('TestModelDescription!!!');

        $owner = UserFactory::createTestUser("ModelTest_Owner");
        $this->modFlowModel->setOwner($owner);

        $participant = UserFactory::createTestUser("ModelTest_Participant");
        $this->modFlowModel->addParticipant($participant);

        $modelArea = AreaFactory::create();
        $modelArea->setId(13);
        $modelArea->setName("TestModelArea");
        $modelArea->setOwner($owner);
        $modelArea->addProperty(PropertyFactory::create());

        $this->modFlowModel->setArea($modelArea);

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setId(12);
        $this->soilModel->setOwner($owner);
        $this->soilModel->setPublic(true);
        $this->soilModel->setName('SoilModel_TestCase');

        $soilModelArea = AreaFactory::create();
        $soilModelArea->setId(15);
        $soilModelArea->setName("TestSoilModelArea");
        $soilModelArea->setOwner($owner);
        $soilModelArea->addProperty(PropertyFactory::create());
        $this->soilModel->setArea($soilModelArea);

        $this->modFlowModel->setSoilModel($this->soilModel);

        $this->layer = GeologicalLayerFactory::create();
        $this->layer->setId(21);
        $this->layer->setOwner($owner);
        $this->layer->setPublic(true);
        $this->layer->setName("ModelTest_Layer");
        $this->soilModel->addGeologicalLayer($this->layer);

        $geounit = GeologicalUnitFactory::create();
        $geounit->setOwner($owner);
        $geounit->setName("TestUnit");
        $this->soilModel->addGeologicalUnit($geounit);

        $geopoint = GeologicalPointFactory::create();
        $geopoint->setOwner($owner);
        $geopoint->setName("TestPoint");
        $this->soilModel->addGeologicalPoint($geopoint);

        $propertyType = PropertyTypeFactory::create();
        $propertyType->setName("KF-X")->setAbbreviation("kx");

        $propertyValue = PropertyValueFactory::create();
        $propertyValue->setValue(1.9991);

        $this->property = PropertyFactory::create()
            ->setId(22)
            ->setName("ModelTest_Property_kx")
            ->setPropertyType($propertyType)
            ->addValue($propertyValue)
        ;
        $this->layer->addProperty($this->property);

        $propertyType = PropertyTypeFactory::create();
        $propertyType->setName("KF-Y")->setAbbreviation("ky");

        $propertyValue = PropertyValueFactory::create();
        $raster = RasterFactory::createEntity();
        $raster->setId(31);
        $propertyValue->setRaster($raster);

        $this->property = PropertyFactory::create()
            ->setId(23)
            ->setName("ModelTest_Property_ky")
            ->setPropertyType($propertyType)
            ->addValue($propertyValue)
        ;

        $this->layer->addProperty($this->property);
    }

    public function testRenderJson()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);

        $serializedModel = json_decode($serializedModel);

        $this->assertEquals($this->modFlowModel->getId(), $serializedModel->id);
        $this->assertEquals($this->modFlowModel->getName(), $serializedModel->name);
        $this->assertEquals($this->modFlowModel->getDescription(), $serializedModel->description);
        $this->assertObjectHasAttribute("area", $serializedModel);
        $this->assertEquals($this->modFlowModel->getArea()->getId(), $serializedModel->area->id);
        $this->assertEquals(1, count((array)$serializedModel->area));

        $this->assertEquals($this->soilModel->getId(), $serializedModel->soil_model->id);
        $this->assertEquals($this->soilModel->getName(), $serializedModel->soil_model->name);
        $this->assertEquals($this->soilModel->getPublic(), $serializedModel->soil_model->public);
        $this->assertObjectHasAttribute("area", $serializedModel->soil_model);
        $this->assertEquals($this->modFlowModel->getSoilModel()->getArea()->getId(), $serializedModel->soil_model->area->id);
        $this->assertEquals(1, count((array)$serializedModel->area));
        $this->assertObjectNotHasAttribute("geological_units", $serializedModel->soil_model);
        $this->assertObjectNotHasAttribute("geological_points", $serializedModel->soil_model);

        $this->assertCount(1, $serializedModel->soil_model->geological_layers);
        $this->assertEquals($this->layer->getId(), $serializedModel->soil_model->geological_layers[0]->id);
        $this->assertEquals($this->layer->getName(), $serializedModel->soil_model->geological_layers[0]->name);

        $this->assertCount(2, $serializedModel->soil_model->geological_layers[0]->properties);
        $this->assertEquals($this->layer->getProperties()->toArray()[0]->getName(), $serializedModel->soil_model->geological_layers[0]->properties[0]->name);
        $this->assertEquals($this->layer->getProperties()->toArray()[0]->getPropertyType()->getAbbreviation(), $serializedModel->soil_model->geological_layers[0]->properties[0]->property_type->abbreviation);
        $this->assertObjectHasAttribute('value', $serializedModel->soil_model->geological_layers[0]->properties[0]->values[0]);
        $this->assertEquals($this->layer->getProperties()->toArray()[0]->getValues()[0]->getValue(), $serializedModel->soil_model->geological_layers[0]->properties[0]->values[0]->value);

        $this->assertEquals($this->layer->getProperties()->toArray()[1]->getName(), $serializedModel->soil_model->geological_layers[0]->properties[1]->name);
        $this->assertEquals($this->layer->getProperties()->toArray()[1]->getPropertyType()->getAbbreviation(), $serializedModel->soil_model->geological_layers[0]->properties[1]->property_type->abbreviation);
        $this->assertObjectHasAttribute('raster', $serializedModel->soil_model->geological_layers[0]->properties[1]->values[0]);
        $this->assertObjectHasAttribute('id', $serializedModel->soil_model->geological_layers[0]->properties[1]->values[0]->raster);
        $this->assertEquals($this->layer->getProperties()->toArray()[1]->getValues()[0]->getRaster()->getId(), $serializedModel->soil_model->geological_layers[0]->properties[1]->values[0]->raster->id);


        $this->assertCount(0, $serializedModel->calculation_properties->stress_periods);
        $this->assertCount(0, $serializedModel->calculation_properties->init_values);
    }
}
