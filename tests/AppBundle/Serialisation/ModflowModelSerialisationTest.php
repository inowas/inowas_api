<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\Property;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
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

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setId(12);
        $this->soilModel->setOwner($owner);
        $this->soilModel->setPublic(true);
        $this->soilModel->setName('SoilModel_TestCase');
        $this->modFlowModel->setSoilModel($this->soilModel);

        $this->layer = GeologicalLayerFactory::create();
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

        $this->property = PropertyFactory::create();
        $this->property->setName("ModelTest_Property");
        $this->property->setPropertyType($propertyType);
        $this->property->addValue($propertyValue);

        $this->layer->addProperty($this->property);
    }

    public function testRenderJson()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);

        $serializedModel = json_decode($serializedModel);

        //var_dump($serializedModel);

        $this->assertEquals($this->modFlowModel->getId(), $serializedModel->id);
        $this->assertEquals($this->modFlowModel->getName(), $serializedModel->name);
        $this->assertEquals($this->modFlowModel->getDescription(), $serializedModel->description);
        $this->assertEquals($this->modFlowModel->getPublic(), $serializedModel->public);

        $this->assertEquals($this->soilModel->getId(), $serializedModel->soil_model->id);
        $this->assertEquals($this->soilModel->getName(), $serializedModel->soil_model->name);
        $this->assertEquals($this->soilModel->getPublic(), $serializedModel->soil_model->public);
        $this->assertObjectNotHasAttribute("geological_units", $serializedModel->soil_model);
        $this->assertObjectNotHasAttribute("geological_points", $serializedModel->soil_model);
        $this->assertCount(1, $serializedModel->soil_model->geological_layers);
        $this->assertEquals($this->layer->getName(), $serializedModel->soil_model->geological_layers[0]->name);
        $this->assertEquals($this->layer->getProperties()->first()->getName(), $serializedModel->soil_model->geological_layers[0]->properties[0]->name);
        $this->assertEquals($this->layer->getProperties()->first()->getPropertyType()->getAbbreviation(), $serializedModel->soil_model->geological_layers[0]->properties[0]->property_type->abbreviation);

        $this->assertCount(0, $serializedModel->calculation_properties->stress_periods);
        $this->assertCount(0, $serializedModel->calculation_properties->init_values);
    }
}
