<?php

namespace AppBundle\Tests\Serialization;

use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\ObservationPoint;
use AppBundle\Entity\Property;
use AppBundle\Entity\SoilModel;
use AppBundle\Entity\StreamBoundary;
use AppBundle\Model\AreaFactory;
use AppBundle\Model\GeneralHeadBoundaryFactory;
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
use AppBundle\Model\StreamBoundaryFactory;
use AppBundle\Model\StressPeriod;
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
        $this->modFlowModel->setName("TestModel");
        $this->modFlowModel->setPublic(true);
        $this->modFlowModel->setDescription('TestModelDescription!!!');

        $owner = UserFactory::createTestUser("ModelTest_Owner");
        $this->modFlowModel->setOwner($owner);

        $modelArea = AreaFactory::create();
        $modelArea->setName("TestModelArea");
        $modelArea->setOwner($owner);
        $modelArea->addProperty(PropertyFactory::create());

        $this->modFlowModel->setArea($modelArea);

        $this->soilModel = SoilModelFactory::create();
        $this->soilModel->setOwner($owner);
        $this->soilModel->setPublic(true);
        $this->soilModel->setName('SoilModel_TestCase');

        $soilModelArea = AreaFactory::create();
        $soilModelArea->setName("TestSoilModelArea");
        $soilModelArea->setOwner($owner);
        $soilModelArea->addProperty(PropertyFactory::create());
        $this->soilModel->setArea($soilModelArea);

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

        $this->property = PropertyFactory::create()
            ->setName("ModelTest_Property_kx")
            ->setPropertyType($propertyType)
            ->addValue($propertyValue)
        ;
        $this->layer->addProperty($this->property);

        $propertyType = PropertyTypeFactory::create();
        $propertyType->setName("KF-Y")->setAbbreviation("ky");

        $propertyValue = PropertyValueFactory::create();
        $raster = RasterFactory::create();
        $propertyValue->setRaster($raster);

        $this->property = PropertyFactory::create()
            ->setName("ModelTest_Property_ky")
            ->setPropertyType($propertyType)
            ->addValue($propertyValue)
        ;

        $this->layer->addProperty($this->property);

        /** @var StreamBoundary $stream */
        $stream = StreamBoundaryFactory::create()
            ->setOwner($owner)
            ->setName("Streamname")
            ->addObservationPoint(ObservationPointFactory::create())
            ->addProperty(PropertyFactory::create())
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime());

        $this->modFlowModel->addBoundary($stream);
        $this->modFlowModel->addBoundary(StreamBoundaryFactory::create()->setId(28));
        $this->modFlowModel->addBoundary(StreamBoundaryFactory::create()->setId(29));

        /** @var GeneralHeadBoundary $boundary */
        $boundary = GeneralHeadBoundaryFactory::create()
            ->setOwner($owner)
            ->setName('BoundaryName')
            ->addObservationPoint(ObservationPointFactory::create())
            ->addProperty(PropertyFactory::create())
            ->setDateCreated(new \DateTime())
            ->setDateModified(new \DateTime());

        $this->modFlowModel->addBoundary($boundary);
        $this->modFlowModel->addBoundary(GeneralHeadBoundaryFactory::create()->setId(39));

        /** @var ObservationPoint $observationPoint */
        $observationPoint = ObservationPointFactory::create()
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
        $this->assertObjectNotHasAttribute("head_from_top_elevation", $serializedModel->calculation_properties->initial_values);
        $this->assertObjectHasAttribute("steady_state_calculation", $serializedModel->calculation_properties->initial_values);
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
            ->setRaster(RasterFactory::create())
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
            "grid_size" => array(
                "rows" => 50,
                "cols" => 60
            ),
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
        $this->assertObjectHasAttribute("grid_size", $serializedModel->calculation_properties);
        $this->assertObjectHasAttribute("rows", $serializedModel->calculation_properties->grid_size);
        $this->assertEquals("50", $serializedModel->calculation_properties->grid_size->rows);
        $this->assertObjectHasAttribute("cols", $serializedModel->calculation_properties->grid_size);
        $this->assertEquals("60", $serializedModel->calculation_properties->grid_size->cols);
        $this->assertObjectHasAttribute("stress_periods", $serializedModel->calculation_properties);
        $this->assertObjectHasAttribute("initial_values", $serializedModel->calculation_properties);
        $this->assertObjectHasAttribute("transient", $serializedModel->calculation_properties);
        $this->assertObjectHasAttribute("recalculation", $serializedModel->calculation_properties);
        $this->assertFalse($serializedModel->calculation_properties->recalculation);

        $this->modFlowModel->setCalculationProperties(array(
            "stress_periods" => array(),
            "initial_values" => array(
                "property" => null,
                "head_from_top_elevation" => 1,
                "steady_state_calculation" => null
            )
        ));

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $serializedModel = $this->serializer->serialize($this->modFlowModel, 'json', $serializationContext);
        $this->assertStringStartsWith('{',$serializedModel);
        $serializedModel = json_decode($serializedModel);

        $this->assertObjectHasAttribute("calculation_properties", $serializedModel);
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
        $this->assertEquals(4, count((array)$serializedModel->calculation_properties->stress_periods[0]));

        $this->assertObjectHasAttribute("date_time_begin", $serializedModel->calculation_properties->stress_periods[0]);
        $this->assertObjectHasAttribute("date_time_end", $serializedModel->calculation_properties->stress_periods[0]);
        $this->assertObjectHasAttribute("number_of_time_steps", $serializedModel->calculation_properties->stress_periods[0]);
        $this->assertObjectHasAttribute("steady", $serializedModel->calculation_properties->stress_periods[0]);

        /** @var StressPeriod $stressPeriod */
        $stressPeriod = $this->modFlowModel->getStressPeriods()[0];
        $this->assertEquals($stressPeriod->getDateTimeBegin(), new \DateTime($serializedModel->calculation_properties->stress_periods[0]->date_time_begin));
        $this->assertEquals($stressPeriod->getDateTimeEnd(), new \DateTime($serializedModel->calculation_properties->stress_periods[0]->date_time_end));
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

        $this->assertEquals($this->soilModel->getId(), $serializedModel->soil_model->id);
        $this->assertObjectHasAttribute("area", $serializedModel->soil_model);
        $this->assertEquals($this->modFlowModel->getSoilModel()->getArea()->getId(), $serializedModel->soil_model->area->id);
        $this->assertObjectNotHasAttribute("geological_units", $serializedModel->soil_model);
        $this->assertObjectNotHasAttribute("geological_points", $serializedModel->soil_model);

        $this->assertCount(1, $serializedModel->soil_model->geological_layers);
        $this->assertEquals($this->layer->getId(), $serializedModel->soil_model->geological_layers[0]->id);

        $this->assertObjectHasAttribute("boundaries", $serializedModel);
        $this->assertCount(5, $serializedModel->boundaries);

        $this->assertObjectHasAttribute("observation_points", $serializedModel);
        $this->assertCount(4, $serializedModel->observation_points);
    }
}
