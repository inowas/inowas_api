<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Laywet;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Soilmodel\HydraulicAnisotropy;
use Inowas\Common\Soilmodel\HydraulicConductivityX;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\VerticalHydraulicConductivity;
use Inowas\Modflow\Model\Command\AddBoundary;
use Inowas\Modflow\Model\Command\AddModflowScenario;
use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Command\ChangeModflowModelGridSize;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\Command\UpdateBoundaryGeometry;
use Inowas\Modflow\Model\Command\UpdateCalculationPackageParameter;
use Inowas\Modflow\Model\Command\UpdateCalculationStressperiods;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\ModflowModelAggregate;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Id\UserId;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Soilmodel\Model\Command\AddGeologicalLayerToSoilmodel;
use Inowas\Soilmodel\Model\Command\ChangeSoilmodelDescription;
use Inowas\Soilmodel\Model\Command\ChangeSoilmodelName;
use Inowas\Soilmodel\Model\Command\CreateSoilmodel;
use Inowas\Soilmodel\Model\Command\UpdateGeologicalLayerProperty;
use Inowas\Soilmodel\Model\GeologicalLayer;
use Inowas\Soilmodel\Model\GeologicalLayerDescription;
use Inowas\Soilmodel\Model\GeologicalLayerId;
use Inowas\Soilmodel\Model\GeologicalLayerName;
use Inowas\Soilmodel\Model\GeologicalLayerNumber;
use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelDescription;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Model\SoilmodelName;

class ModflowModelEventSourcingTest extends EventSourcingBaseTest
{
    public function test_create_modflow_model(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modelId));

        /** @var ModflowModelAggregate $model */
        $model = $this->container->get('modflow_model_list')->get($modelId);
        $this->assertInstanceOf(ModflowModelAggregate::class, $model);
    }

    public function test_modflow_event_bus(): void
    {
        $ownerId = UserId::generate();
        $modflowModelId = ModflowId::generate();
        $event = ModflowModelWasCreated::byUserWithModflowIdAndUnits(
            $ownerId,
            $modflowModelId,
            LengthUnit::fromInt(LengthUnit::METERS),
            TimeUnit::fromInt(TimeUnit::DAYS)
        );

        $this->eventBus->dispatch($event);
    }

    public function test_setup_model_name_gridsize_and_bounding_box(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();
        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId,$modelId, Modelname::fromString('TestModel')));

        $box = $this->container->get('inowas.geotools')->projectBoundingBox(BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615, 4326), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $gridSize = GridSize::fromXY(75, 40);
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modelId, $gridSize));

        /** @var ModflowModelAggregate $model */
        $model = $this->container->get('modflow_model_list')->get($modelId);
        $this->assertInstanceOf(ModflowModelAggregate::class, $model);
        $this->assertEquals(Modelname::fromString('TestModel'), $model->name());
        $this->assertEquals($gridSize, $model->gridSize());
        $this->assertEquals($boundingBox, $model->boundingBox());
    }

    public function test_add_area_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $area = $this->createAreaBoundary();

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $area));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findAreaActiveCells($modelId);
        $this->assertCount(1610, $activeCells->cells());

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $area->boundaryId());
        $this->assertCount(1610, $activeCells->cells());
    }

    public function test_add_wel_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $wellBoundary = $this->createWellBoundary();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $wellBoundary->boundaryId());
        $this->assertCount(1, $activeCells->cells());
        $this->assertEquals([[0, 8, 10]], $activeCells->cells());
    }

    public function test_create_soilmodel_aggregate(): void
    {
        $ownerId = UserId::generate();
        $soilModelId = SoilmodelId::generate();
        $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId($ownerId, $soilModelId));
        $this->commandBus->dispatch(ChangeSoilmodelName::forSoilmodel($ownerId, $soilModelId, SoilmodelName::fromString('testSoilmodel')));
        $this->commandBus->dispatch(ChangeSoilmodelDescription::forSoilmodel($ownerId, $soilModelId, SoilmodelDescription::fromString('testSoilmodelDescription')));

        /** @var SoilmodelAggregate $soilmodel */
        $soilmodel = $this->container->get('soil_model_list')->getAggregateRoot($soilModelId->toString());
        $this->assertInstanceOf(SoilmodelAggregate::class, $soilmodel);
        $this->assertEquals(SoilmodelName::fromString('testSoilmodel'), $soilmodel->name());
        $this->assertEquals(SoilmodelDescription::fromString('testSoilmodelDescription'), $soilmodel->description());
    }

    public function test_add_layers_to_soilmodel_aggregate(): void
    {
        $ownerId = UserId::generate();
        $soilModelId = SoilmodelId::generate();
        $this->createSoilmodel($ownerId, $soilModelId);

        $geologicalLayerId = GeologicalLayerId::generate();
        $layer = GeologicalLayer::fromParams(
            $geologicalLayerId,
            Laytyp::fromInt(Laytyp::TYPE_CONVERTIBLE),
            GeologicalLayerNumber::fromInteger(0),
            GeologicalLayerName::fromString('TestLayer'),
            GeologicalLayerDescription::fromString('TestLayer Description')
        );

        $this->commandBus->dispatch(AddGeologicalLayerToSoilmodel::forSoilmodel($ownerId, $soilModelId, $layer));

        /** @var SoilmodelAggregate $soilmodel */
        $soilmodel = $this->container->get('soil_model_list')->getAggregateRoot($soilModelId->toString());
        $this->assertCount(1, $soilmodel->layers());
        $this->assertEquals($layer, $soilmodel->getGeologicalLayer($geologicalLayerId));
    }

    public function test_update_layer_values_in_soilmodel_aggregate(): void
    {
        $ownerId = UserId::generate();
        $soilModelId = SoilmodelId::generate();
        $this->createSoilmodel($ownerId, $soilModelId);

        $geologicalLayerId = GeologicalLayerId::generate();
        $layer = GeologicalLayer::fromParams(
            $geologicalLayerId,
            Laytyp::fromInt(Laytyp::TYPE_CONVERTIBLE),
            GeologicalLayerNumber::fromInteger(0),
            GeologicalLayerName::fromString('TestLayer'),
            GeologicalLayerDescription::fromString('TestLayer Description')
        );
        $this->commandBus->dispatch(AddGeologicalLayerToSoilmodel::forSoilmodel($ownerId, $soilModelId, $layer));

        $properties = array(
            array('value' => TopElevation::fromLayerValue(100.01), 'getter' => 'hTop'),
            array('value' => TopElevation::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'hTop'),
            array('value' => BottomElevation::fromLayerValue(100.01), 'getter' => 'hBottom'),
            array('value' => BottomElevation::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'hBottom'),
            array('value' => HydraulicConductivityX::fromLayerValue(100.01), 'getter' => 'hydraulicConductivityX'),
            array('value' => HydraulicConductivityX::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'hydraulicConductivityX'),
            array('value' => HydraulicAnisotropy::fromLayerValue(100.01), 'getter' => 'hydraulicAnisotropy'),
            array('value' => HydraulicAnisotropy::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'hydraulicAnisotropy'),
            array('value' => VerticalHydraulicConductivity::fromLayerValue(100.01), 'getter' => 'verticalHydraulicConductivity'),
            array('value' => VerticalHydraulicConductivity::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'verticalHydraulicConductivity'),
            array('value' => SpecificStorage::fromLayerValue(100.01), 'getter' => 'specificStorage'),
            array('value' => SpecificStorage::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'specificStorage'),
            array('value' => SpecificYield::fromLayerValue(100.01), 'getter' => 'specificYield'),
            array('value' => SpecificYield::fromLayerValue([[1,2,3], [1,2,3]]), 'getter' => 'specificYield'),
        );

        foreach ($properties as $property) {

            $value = $property['value'];
            $getter = $property['getter'];

            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $geologicalLayerId, $value));

            /** @var SoilmodelAggregate $soilmodel */
            $soilmodel = $this->container->get('soil_model_list')->getAggregateRoot($soilModelId->toString());

            /** @var GeologicalLayer $layer */
            $layer = $soilmodel->getGeologicalLayer($geologicalLayerId);
            $this->assertEquals($property['value'], $layer->values()->{$getter}());
        }
    }

    public function test_add_riv_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $riverBoundary = $this->createRiverBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $riverBoundary));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $riverBoundary->boundaryId());
        $this->assertCount(135, $activeCells->cells());
    }

    public function test_add_chd_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $chdBoundary = $this->createConstantHeadBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $chdBoundary));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $chdBoundary->boundaryId());
        $this->assertCount(75, $activeCells->cells());
    }

    public function test_add_ghb_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $ghbBoundary = $this->createGeneralHeadBoundaryWithObservationPoint();

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $ghbBoundary));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $ghbBoundary->boundaryId());
        $this->assertCount(75, $activeCells->cells());
    }

    public function test_add_rch_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $rchBoundary = $this->createRechargeBoundary();

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $rchBoundary));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $rchBoundary->boundaryId());
        $this->assertCount(1610, $activeCells->cells());
    }

    public function test_create_steady_calculation_from_model_with_two_well_boundaries(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $wellBoundary = WellBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.671125, -31.325009, 4326)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(new \DateTimeImmutable('2015-01-01'), -5000));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $boundaryId = BoundaryId::generate();
        $wellBoundary = WellBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('Test Well 2'),
            Geometry::fromPoint(new Point(-63.659952, -31.330144, 4326)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(new \DateTimeImmutable('2015-01-01'), -2000));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);

        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('wel', $data->packages);
        $wel =  $data->wel;
        $this->assertObjectHasAttribute('stress_period_data', $wel);
        $stressperiodData = (array)$wel->stress_period_data;
        $this->assertCount(1, $stressperiodData);
        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(2, $dataForFirstStressPeriod);
        $this->assertContains([0, 12, 17, -2000], $dataForFirstStressPeriod);
        $this->assertContains([0,  8, 10, -5000], $dataForFirstStressPeriod);
    }

    public function test_create_calculation_from_model_with_two_stress_periods_and_two_well_boundaries_on_the_same_grid_cell_should_sum_up_pumping_rates(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $wellBoundary = WellBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.671125, -31.325009, 4326)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(new \DateTimeImmutable('2015-01-01'), -5000));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $boundaryId = BoundaryId::generate();
        $wellBoundary = WellBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('Test Well 2'),
            Geometry::fromPoint(new Point(-63.671126, -31.325010, 4326)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(new \DateTimeImmutable('2015-01-01'), -2000));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $stressperiods->addStressPeriod(StressPeriod::create(1, 100,1,1,false));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);

        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('wel', $data->packages);
        $wel =  $data->wel;
        $this->assertObjectHasAttribute('stress_period_data', $wel);
        $stressperiodData = (array)$wel->stress_period_data;
        $this->assertCount(2, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(1, $dataForFirstStressPeriod);
        $this->assertContains([0,  8, 10, -7000], $dataForFirstStressPeriod);

        $dataForSecondStressPeriod = array_values($stressperiodData)[1];
        $this->assertCount(1, $dataForSecondStressPeriod);
        $this->assertContains([0,  8, 10, -7000], $dataForSecondStressPeriod);
    }

    public function test_create_steady_calculation_from_model_with_chd_boundary_with_one_observationpoint(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $chdBoundary = $this->createConstantHeadBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $chdBoundary));

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $chdBoundary->boundaryId());
        $numberOfActiveCells = count($activeCells->cells());

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);

        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('chd', $data->packages);
        $chd =  $data->chd;
        $this->assertObjectHasAttribute('stress_period_data', $chd);
        $stressperiodData = (array)$chd->stress_period_data;
        $this->assertCount(1, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount($numberOfActiveCells, $dataForFirstStressPeriod);
    }

    public function test_create_steady_calculation_from_model_with_ghb_boundary_with_one_observationpoint(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $ghbBoundary = $this->createGeneralHeadBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $ghbBoundary));

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $ghbBoundary->boundaryId());
        $numberOfActiveCells = count($activeCells->cells());

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);

        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('ghb', $data->packages);
        $ghb =  $data->ghb;
        $this->assertObjectHasAttribute('stress_period_data', $ghb);
        $stressperiodData = (array)$ghb->stress_period_data;
        $this->assertCount(1, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount($numberOfActiveCells, $dataForFirstStressPeriod);
    }

    public function test_create_steady_calculation_from_model_with_rch_boundary(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $rchBoundary = $this->createRechargeBoundary();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $rchBoundary));

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);

        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('rch', $data->packages);
        $rch =  $data->rch;
        $this->assertObjectHasAttribute('stress_period_data', $rch);
        $stressperiodData = (array)$rch->stress_period_data;
        $this->assertCount(1, $stressperiodData);

        $stressperiodDataFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(40, $stressperiodDataFirstStressPeriod);
        $this->assertCount(75, $stressperiodDataFirstStressPeriod[0]);
        $this->assertEquals(0.000329, $stressperiodDataFirstStressPeriod[0][23]);
    }

    public function test_create_steady_calculation_from_model_with_riv_boundary_with_one_observationpoint(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $riverBoundary = $this->createRiverBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $riverBoundary));

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $riverBoundary->boundaryId());
        $numberOfActiveCells = count($activeCells->cells());

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);

        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('riv', $data->packages);
        $riv =  $data->riv;
        $this->assertObjectHasAttribute('stress_period_data', $riv);
        $stressperiodData = (array)$riv->stress_period_data;
        $this->assertCount(1, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount($numberOfActiveCells, $dataForFirstStressPeriod);
    }

    public function test_update_calculation_packages_lpf_layTyp(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $this->commandBus->dispatch(UpdateCalculationPackageParameter::byUserWithModelId($calculationId, $ownerId, $modelId, 'lpf','layTyp', Laytyp::fromArray(array(1))));

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);
        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('lpf', $data->packages);
        $lpf =  $data->lpf;
        $this->assertObjectHasAttribute('laytyp', $lpf);
        $this->assertEquals([1], $lpf->laytyp);
    }

    public function test_update_calculation_packages_lpf_laywet(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));

        $calculationId = ModflowId::generate();
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId(
            $calculationId,
            $ownerId,
            $modelId,
            $start,
            $end
        ));

        $this->commandBus->dispatch(UpdateCalculationPackageParameter::byUserWithModelId($calculationId, $ownerId, $modelId, 'lpf','layWet', Laywet::fromArray(array(1))));

        $config = $this->container->get('inowas.modflow_projection.calculation_configuration_finder')->getConfigurationJson($calculationId);
        $this->assertJson($config);
        $obj = json_decode($config);
        $this->assertEquals($calculationId->toString(), $obj->id);
        $this->assertEquals('flopy_calculation', $obj->type);
        $this->assertObjectHasAttribute('data', $obj);
        $data = $obj->data;
        $this->assertObjectHasAttribute('packages', $data);
        $this->assertContains('lpf', $data->packages);
        $lpf =  $data->lpf;
        $this->assertObjectHasAttribute('laywet', $lpf);
        $this->assertEquals([1], $lpf->laywet);
    }

    public function test_create_scenario_from_basemodel_with_all_boundary_types(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createAreaBoundary()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createRechargeBoundary()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createWellBoundary()));

        $baseModelBoundaries = $this->container->get('inowas.model_boundaries_finder')->findByModelId($modelId);
        $this->assertCount(6, $baseModelBoundaries);

        $scenarioId = ModflowId::generate();
        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));
        $scenarioBoundaries = $this->container->get('inowas.model_boundaries_finder')->findByModelId($scenarioId);
        $this->assertCount(6, $scenarioBoundaries);
        $this->assertEquals(sort($baseModelBoundaries), sort($scenarioBoundaries));
    }

    public function test_add_well_to_scenario_from_basemodel_with_all_other_boundary_types(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createAreaBoundary()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createRechargeBoundary()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));

        $scenarioId = ModflowId::generate();
        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));
        $this->commandBus->dispatch(AddBoundary::toScenario($ownerId, $modelId, $scenarioId, $this->createWellBoundary()));
        $scenarioBoundaries = $this->container->get('inowas.model_boundaries_finder')->findByModelId($scenarioId);
        $this->assertCount(6, $scenarioBoundaries);
    }

    public function test_move_well_of_scenario_from_basemodel_with_all_boundary_types(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithNameBoundingBoxAndGridSize($ownerId, $modelId);

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createAreaBoundary()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createRechargeBoundary()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $well = $this->createWellBoundary()));

        $scenarioId = ModflowId::generate();
        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));

        $newGeometry = Geometry::fromPoint(new Point(-63.659952, -31.330144, 4326));
        $this->commandBus->dispatch(UpdateBoundaryGeometry::ofScenario($ownerId, $modelId, $scenarioId, $well->boundaryId(), $newGeometry));
        $scenarioBoundaries = $this->container->get('inowas.model_boundaries_finder')->findByModelId($scenarioId);
        $this->assertCount(6, $scenarioBoundaries);

        /** @var WellBoundary[] $wells */
        $wells = $this->container->get('inowas.model_boundaries_finder')->findWells($scenarioId);
        $this->assertCount(1, $wells);

        $well = $wells[0];
        $this->assertEquals($newGeometry, $well->geometry());
        $this->assertEquals([[0,12,17]], $well->activeCells()->cells());

        $observationPoints = $well->observationPoints();
        /** @var ObservationPoint $observationPoint */
        $observationPoint = array_values($observationPoints)[0];
        $this->assertEquals($newGeometry, $observationPoint->geometry());
    }

    public function test_that_moving_a_well_changes_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithSoilmodel($ownerId, $modelId);

        $wellBoundary = $this->createWellBoundary();
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $wellBoundary->boundaryId());
        $this->assertCount(1, $activeCells->cells());
        $this->assertEquals([[0,8,10]], $activeCells->cells());

        $newGeometry = Geometry::fromPoint(new Point(-63.659952, -31.330144, 4326));
        $this->commandBus->dispatch(UpdateBoundaryGeometry::ofBaseModel($ownerId, $modelId, $wellBoundary->boundaryId(), $newGeometry));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $wellBoundary->boundaryId());
        $this->assertCount(1, $activeCells->cells());
        $this->assertEquals([[0,12,17]], $activeCells->cells());
    }
}
