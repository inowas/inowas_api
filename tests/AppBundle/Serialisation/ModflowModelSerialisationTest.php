<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\Boundary;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Property;
use AppBundle\Entity\SoilModel;
use AppBundle\Entity\Stream;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\BoundaryFactory;
use AppBundle\Model\GeologicalLayerFactory;
use AppBundle\Model\GeologicalPointFactory;
use AppBundle\Model\GeologicalUnitFactory;
use AppBundle\Model\ModFlowModelFactory;
use AppBundle\Model\ObservationPointFactory;
use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterFactory;
use AppBundle\Model\SoilModelFactory;
use AppBundle\Model\StreamFactory;
use AppBundle\Model\StressPeriodFactory;
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

    /**
     * @var array Stream
     */
    protected $streams;

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

        /** @var Stream $stream */
        $stream = StreamFactory::create()
            ->setId(27)
            ->setOwner($owner)
            ->setName("Streamname")
            ->addObservationPoint(ObservationPointFactory::create())
            ->addProperty(PropertyFactory::create())
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime());

        $this->modFlowModel->addStream($stream);
        $this->modFlowModel->addStream(StreamFactory::create()->setId(28));
        $this->modFlowModel->addStream(StreamFactory::create()->setId(29));

        /** @var Boundary $boundary */
        $boundary = BoundaryFactory::create()
            ->setId(38)
            ->setOwner($owner)
            ->setName('BoundaryName')
            ->addObservationPoint(ObservationPointFactory::create())
            ->addProperty(PropertyFactory::create())
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime());

        $this->modFlowModel->addBoundary($boundary);
        $this->modFlowModel->addBoundary(BoundaryFactory::create()->setId(39));

        /** @var ObservationPoint $observationPoint */
        $observationPoint = ObservationPointFactory::create()
            ->setId(41)
            ->setOwner($owner)
            ->setName('ObservationPoint')
            ->addProperty(PropertyFactory::create())
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime());

        $this->modFlowModel->addObservationPoint($observationPoint);
        $this->modFlowModel->addObservationPoint(ObservationPointFactory::create()->setId(42));
        $this->modFlowModel->addObservationPoint(ObservationPointFactory::create()->setId(43));
        $this->modFlowModel->addObservationPoint(ObservationPointFactory::create()->setId(44));
    }

    public function testCalculationPropertiesInitialValuesDefault()
    {
        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);

        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("initial_values", $serializedModel->calculation_properties);
        $this->assertObjectNotHasAttribute("property", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectHasAttribute("head_from_top_elevation", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("steady_state_calculation", $serializedModel->calculation_properties->initial_values);
    }

    public function testCalculationPropertiesInitValueIsAPropertyWithFloatValue()
    {
        $property = PropertyFactory::create()->setId(5);
        $property->addValue(PropertyValueFactory::create()->setValue(1.114));

        $this->modFlowModel->setInitialValues(array(
            "property" => $property,
            "head_from_top_elevation" => null,
            "steady_state_calculation" => null
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');
        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);
        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("initial_values", $serializedModel->calculation_properties);
        $this->assertObjectHasAttribute("property", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("head_from_top_elevation", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("steady_state_calculation", $serializedModel->calculation_properties->initial_values);
        $this->assertEquals($property->getId(), $serializedModel->calculation_properties->initial_values->property->id);
        $this->assertEquals($property->getValues()->first()->getValue(), $serializedModel->calculation_properties->initial_values->property->values[0]->value);
    }

    public function testCalculationPropertiesInitValueIsAPropertyWithRaster()
    {
        $property = PropertyFactory::create()->setId(5);
        $property->addValue(PropertyValueFactory::create()
            ->setRaster(RasterFactory::createEntity()->setId(32))
        );

        $this->modFlowModel->setInitialValues(array(
            "property" => $property,
            "head_from_top_elevation" => null,
            "steady_state_calculation" => null
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');
        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);
        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("initial_values", $serializedModel->calculation_properties);
        $this->assertObjectHasAttribute("property", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("head_from_top_elevation", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("steady_state_calculation", $serializedModel->calculation_properties->initial_values);
        $this->assertEquals($property->getId(), $serializedModel->calculation_properties->initial_values->property->id);
        $this->assertEquals($property->getValues()->first()->getRaster()->getId(), $serializedModel->calculation_properties->initial_values->property->values[0]->raster->id);
    }

    public function testCalculationPropertiesInitValueIsHeadFromTopElevation()
    {
        $this->modFlowModel->setInitialValues(array(
            "property" => null,
            "head_from_top_elevation" => 1.557,
            "steady_state_calculation" => null
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');
        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);
        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("initial_values", $serializedModel->calculation_properties);
        $this->assertObjectNotHasAttribute("property", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectHasAttribute("head_from_top_elevation", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("steady_state_calculation", $serializedModel->calculation_properties->initial_values);
        $this->assertEquals($this->modFlowModel->getInitialValues()["head_from_top_elevation"], $serializedModel->calculation_properties->initial_values->head_from_top_elevation);
    }

    public function testCalculationPropertiesInitValueIsSteadyStateCalculation()
    {
        $this->modFlowModel->setInitialValues(array(
            "property" => null,
            "head_from_top_elevation" => null,
            "steady_state_calculation" => 123
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');
        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);
        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("initial_values", $serializedModel->calculation_properties);
        $this->assertObjectNotHasAttribute("property", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectNotHasAttribute("head_from_top_elevation", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectHasAttribute("steady_state_calculation", $serializedModel->calculation_properties->initial_values);
        $this->assertEquals($this->modFlowModel->getInitialValues()["steady_state_calculation"], $serializedModel->calculation_properties->initial_values->steady_state_calculation);
    }

    public function testCalculationPropertiesSteadyStateCalculation()
    {
        $this->modFlowModel->setCalculationProperties(array(
            "stress_periods" => array(),
            "initial_values" => array(
                "property" => null,
                "head_from_top_elevation" => 1,
                "steady_state_calculation" => null
            ),
            "calculation_type" => "steady_state",
            "transient" => null,
            "recalculation" => true
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("calculation_type", $serializedModel->calculation_properties);
        $this->assertEquals("steady_state", $serializedModel->calculation_properties->calculation_type);
    }

    public function testCalculationPropertiesTransientCalculation()
    {
        $this->modFlowModel->setCalculationProperties(array(
            "stress_periods" => array(),
            "initial_values" => array(
                "property" => null,
                "head_from_top_elevation" => 1,
                "steady_state_calculation" => null
            ),
            "calculation_type" => "steady_state",
            "recalculation" => true
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("calculation_type", $serializedModel->calculation_properties);
        $this->assertEquals("steady_state", $serializedModel->calculation_properties->calculation_type);
    }

    public function testCalculationPropertiesRecalculation()
    {
        $this->modFlowModel->setCalculationProperties(array(
            "stress_periods" => array(),
            "initial_values" => array(
                "property" => null,
                "head_from_top_elevation" => 1,
                "steady_state_calculation" => null
            ),
            "steady_state" => null,
            "transient" => true,
            "recalculation" => false
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("recalculation", $serializedModel->calculation_properties);
        $this->assertFalse($serializedModel->calculation_properties->recalculation);

        $this->modFlowModel->setCalculationProperties(array(
            "stress_periods" => array(),
            "initial_values" => array(
                "property" => null,
                "head_from_top_elevation" => 1,
                "steady_state_calculation" => null
            ),
            "steady_state" => null,
            "transient" => true,
            "recalculation" => true
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("recalculation", $serializedModel->calculation_properties);
        $this->assertTrue($serializedModel->calculation_properties->recalculation);
    }

    public function testCalculationPropertiesStressPeriods()
    {
        $this->modFlowModel->addStressPeriod(
            StressPeriodFactory::create()
                ->setDateTimeBegin(new \DateTime('1-1-2000'))
                ->setDateTimeEnd(new \DateTime('2-1-2000'))
        );

        $this->modFlowModel->addStressPeriod(
            StressPeriodFactory::create()
                ->setDateTimeBegin(new \DateTime('2-1-2000'))
                ->setDateTimeEnd(new \DateTime('3-1-2000'))
        );

        $this->modFlowModel->addStressPeriod(
            StressPeriodFactory::create()
                ->setDateTimeBegin(new \DateTime('3-1-2000'))
                ->setDateTimeEnd(new \DateTime('4-1-2000'))
        );

        $this->modFlowModel->addStressPeriod(
            StressPeriodFactory::create()
                ->setDateTimeBegin(new \DateTime('5-1-2000'))
                ->setDateTimeEnd(new \DateTime('6-1-2000'))
        );
        $this->modFlowModel->addStressPeriod(
            StressPeriodFactory::create()
                ->setDateTimeBegin(new \DateTime('7-1-2000'))
                ->setDateTimeEnd(new \DateTime('8-1-2000'))
        );

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);

        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
        $this->assertObjectHasAttribute("stress_periods", $serializedModel->calculation_properties);
        $this->assertCount(5, $serializedModel->calculation_properties->stress_periods);
        $this->assertEquals(2, count((array)$serializedModel->calculation_properties->stress_periods[0]));
        $this->assertObjectHasAttribute("date_time_begin", $serializedModel->calculation_properties->stress_periods[0]);
        $this->assertObjectHasAttribute("date_time_end", $serializedModel->calculation_properties->stress_periods[0]);
        $this->assertEquals($this->modFlowModel->getStressPeriods()[0]->getDateTimeBegin(), new \DateTime($serializedModel->calculation_properties->stress_periods[0]->date_time_begin));
        $this->assertEquals($this->modFlowModel->getStressPeriods()[0]->getDateTimeEnd(), new \DateTime($serializedModel->calculation_properties->stress_periods[0]->date_time_end));
    }

    public function testOutputOptionsPointCalculation()
    {
        
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

        $this->assertObjectHasAttribute("streams", $serializedModel);
        $this->assertCount(3, $serializedModel->streams);
        $this->assertEquals(1, count((array)$serializedModel->streams[0]));
        $this->assertEquals($this->modFlowModel->getStreams()->toArray()[0]->getId(), $serializedModel->streams[0]->id);
        $this->assertEquals($this->modFlowModel->getStreams()->toArray()[1]->getId(), $serializedModel->streams[1]->id);
        $this->assertEquals($this->modFlowModel->getStreams()->toArray()[2]->getId(), $serializedModel->streams[2]->id);

        $this->assertObjectHasAttribute("boundaries", $serializedModel);
        $this->assertCount(2, $serializedModel->boundaries);
        $this->assertEquals(1, count((array)$serializedModel->boundaries[0]));

        $this->assertObjectHasAttribute("observation_points", $serializedModel);
        $this->assertCount(4, $serializedModel->observation_points);
        $this->assertEquals(1, count((array)$serializedModel->observation_points[0]));
    }
}
