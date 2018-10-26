<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Inowas\Common\Boundaries\HeadObservationWell;
use Inowas\Common\Boundaries\HeadObservationWellDateTimeValue;
use Inowas\Common\Boundaries\Metadata;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Grid\AffectedCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Laywet;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\ModflowModel;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Modflow\Optimization;
use Inowas\Common\Modflow\OptimizationInput;
use Inowas\Common\Modflow\OptimizationMethod;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\ParameterName;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\Version;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\AddLayer;
use Inowas\ModflowModel\Model\Command\ChangeBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeFlowPackage;
use Inowas\ModflowModel\Model\Command\ChangeGridSize;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\Command\RemoveBoundary;
use Inowas\ModflowModel\Model\Command\UpdateBoundary;
use Inowas\ModflowModel\Model\Command\UpdateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateModflowPackageParameter;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationCalculationState;
use Inowas\ModflowModel\Model\Command\UpdateOptimizationInput;
use Inowas\ModflowModel\Model\Command\UpdateStressPeriods;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Id\UserId;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
use Inowas\Tool\Model\ToolId;
use Prooph\ServiceBus\Exception\CommandDispatchException;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class ModflowModelEventSourcingTest extends EventSourcingBaseTest
{
    /**
     *
     */
    public function test_create_modflow_model(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        /** @var ModflowModelAggregate $model */
        $model = $this->container->get('modflow_model_list')->get($modelId);
        $this->assertInstanceOf(ModflowModelAggregate::class, $model);
    }

    /**
     *
     */
    public function test_modflow_event_bus(): void
    {
        $ownerId = UserId::generate();
        $modflowModelId = ModflowId::generate();
        $event = NameWasChanged::byUserWithName(
            $ownerId,
            $modflowModelId,
            Name::fromString('newName')
        );

        $this->eventBus->dispatch($event);
    }

    /**
     * @throws \Exception
     */
    public function test_setup_model_with_area_and_grid_size(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();
        $modelName = Name::fromString('TestModel444');
        $modelDescription = Description::fromString('TestModelDescription444');

        $polygon = $this->createPolygon();
        $boundingBox = $this->container->get('inowas.geotools.geotools_service')->getBoundingBox(Geometry::fromPolygon($polygon));
        $gridSize = GridSize::fromXY(75, 40);
        $this->commandBus->dispatch(
            CreateModflowModel::newWithAllParams(
                $ownerId,
                $modelId,
                $modelName,
                $modelDescription,
                $polygon,
                $gridSize,
                $boundingBox,
                TimeUnit::fromInt(1),
                LengthUnit::fromInt(2),
                Visibility::public()
            )
        );

        /** @var ModflowModelAggregate $model */
        $model = $this->container->get('modflow_model_list')->get($modelId);
        $this->assertInstanceOf(ModflowModelAggregate::class, $model);

        $modelFinder = $this->container->get('inowas.modflowmodel.model_finder');

        $this->assertEquals($modelName, $modelFinder->getModelNameByModelId($modelId));
        $this->assertEquals($modelDescription, $modelFinder->getModelDescriptionByModelId($modelId));
        $this->assertEquals($gridSize, $modelFinder->getGridSizeByModflowModelId($modelId));
    }

    /**
     * @throws \Exception
     */
    public function test_setup_model_and_change_model_bounding_box_and_grid_size(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();

        $this->createModelWithOneLayer($ownerId, $modelId);
        $boundingBox = BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615);
        $this->commandBus->dispatch(ChangeBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $gridSize = GridSize::fromXY(80, 30);
        $this->commandBus->dispatch(ChangeGridSize::forModflowModel($ownerId, $modelId, $gridSize));

        $modelFinder = $this->container->get('inowas.modflowmodel.model_finder');
        $this->assertEquals($boundingBox, $modelFinder->getBoundingBoxByModflowModelId($modelId));
        $this->assertEquals($gridSize, $modelFinder->getGridSizeByModflowModelId($modelId));
    }

    /**
     * @throws \Exception
     */
    public function test_setup_private_model_and_change_to_public(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();
        $modelName = Name::fromString('TestModel444');
        $modelDescription = Description::fromString('TestModelDescription444');

        $polygon = $this->createPolygon();
        $boundingBox = $this->container->get('inowas.geotools.geotools_service')->getBoundingBox(Geometry::fromPolygon($polygon));
        $gridSize = GridSize::fromXY(75, 40);
        $this->commandBus->dispatch(
            CreateModflowModel::newWithAllParams(
                $ownerId,
                $modelId,
                $modelName,
                $modelDescription,
                $polygon,
                $gridSize,
                $boundingBox,
                TimeUnit::fromInt(1),
                LengthUnit::fromInt(2),
                Visibility::private()
            )
        );

        /** @var ModflowModel $model */
        $model = $this->container->get('inowas.modflowmodel.manager')->findModel($modelId, $ownerId);
        $this->assertFalse($model->visibility()->isPublic());
        $this->assertFalse($this->container->get('inowas.tool.tools_finder')->isPublic(ToolId::fromString($modelId->toString())));

        $this->commandBus->dispatch(UpdateModflowModel::newWithAllParams(
            $ownerId,
            $modelId,
            $modelName,
            $modelDescription,
            $polygon,
            $gridSize,
            $boundingBox,
            TimeUnit::fromInt(1),
            LengthUnit::fromInt(2),
            null,
            Visibility::public()
        ));

        /** @var ModflowModel $model */
        $model = $this->container->get('inowas.modflowmodel.manager')->findModel($modelId, $ownerId);
        $this->assertTrue($model->visibility()->isPublic());
        $this->assertTrue($this->container->get('inowas.tool.tools_finder')->isPublic(ToolId::fromString($modelId->toString())));
    }

    /**
     * @throws \exception
     */
    public function test_update_area_geometry_and_calculate_affected_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $boundingBox = BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615);
        $this->commandBus->dispatch(ChangeBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $activeCells = $this->container->get('inowas.modflowmodel.manager')->getAreaActiveCells($modelId);
        $this->assertCount(1610, $activeCells->cells());
    }

    /**
     * @throws \Exception
     */
    public function test_update_grid_size_updates_affected_cells_of_area_and_boundaries(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $boundingBox = BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615);
        $this->commandBus->dispatch(ChangeBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRechargeBoundaryCenter()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createWellBoundary()));
        $this->commandBus->dispatch(ChangeGridSize::forModflowModel($ownerId, $modelId, GridSize::fromXY(20, 20)));
        $activeCells = $this->container->get('inowas.modflowmodel.manager')->getAreaActiveCells($modelId);
        $this->assertCount(234, $activeCells->cells());
    }

    /**
     *
     */
    public function test_add_layer_to_model(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);
        $layer = $this->createLayer();
        $this->commandBus->dispatch(AddLayer::forModflowModel($ownerId, $modelId, $layer));

        $this->assertEquals($layer, $this->container->get('inowas.modflowmodel.soilmodel_finder')->findLayer($modelId, $layer->id()));
    }

    /**
     * @throws \Exception
     */
    public function test_add_wel_boundary_to_model_and_calculate_affected_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $wellBoundary = $this->createWellBoundary();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        /** @var AffectedCells $affectedCells */
        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $wellBoundary->boundaryId());
        $this->assertCount(1, $affectedCells->cells());
        $this->assertEquals([[53, 8]], $affectedCells->cells());

        /** @var WellBoundary $wellBoundary */
        $wellBoundary = $this->container->get('inowas.modflowmodel.boundary_manager')->getBoundary($modelId, $wellBoundary->boundaryId());
        $this->assertCount(1, $wellBoundary->toArray()['date_time_values']);
        $this->assertEquals('2015-01-01T00:00:00+00:00', $wellBoundary->toArray()['date_time_values'][0]['date_time']);
        $this->assertEquals(-5000, $wellBoundary->toArray()['date_time_values'][0]['values'][0]);
    }

    /**
     * @throws \Exception
     */
    public function test_add_riv_boundary_to_model_and_calculate_affected_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $riverBoundary = $this->createRiverBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $riverBoundary));

        /** @var AffectedCells $affectedCells */
        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $riverBoundary->boundaryId());
        $this->assertCount(131, $affectedCells->cells());
    }

    /**
     * @throws \Exception
     */
    public function test_add_chd_boundary_to_model_and_calculate_affected_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $chdBoundary = $this->createConstantHeadBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $chdBoundary));

        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $chdBoundary->boundaryId());
        $this->assertCount(75, $affectedCells->cells());
    }

    /**
     * @throws \Exception
     */
    public function test_add_ghb_boundary_to_model_and_calculate_affected_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $ghbBoundary = $this->createGeneralHeadBoundaryWithObservationPoint();

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $ghbBoundary));
        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $ghbBoundary->boundaryId());
        $this->assertCount(75, $affectedCells->cells());
    }

    /**
     * @throws \Exception
     */
    public function test_add_rch_boundary_to_model_and_calculate_affected_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $rchBoundary = $this->createRechargeBoundaryCenter();

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $rchBoundary));
        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $rchBoundary->boundaryId());
        $this->assertCount(1430, $affectedCells->cells());
    }

    /**
     * @throws \Exception
     */
    public function test_it_throws_an_exception_if_boundary_to_update_does_not_exist(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $wellBoundary = $this->createWellBoundary();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        $this->expectException(CommandDispatchException::class);
        $this->commandBus->dispatch(UpdateBoundary::forModflowModel($ownerId, $modelId, BoundaryId::fromString('invalid'), $wellBoundary));
    }

    /**
     * @throws \Exception
     */
    public function test_it_throws_an_exception_if_boundary_to_remove_does_not_exist(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $wellBoundary = $this->createWellBoundary();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        $this->expectException(CommandDispatchException::class);
        $this->commandBus->dispatch(RemoveBoundary::forModflowModel($ownerId, $modelId, BoundaryId::fromString('invalid')));
    }

    /**
     * @test
     * @throws \exception
     */
    public function it_creates_a_steady_calculation_checks_that_dis_package_is_available(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->createSteadyCalculation($ownerId, $modelId);
        $jsonRequest = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($jsonRequest);
        $arr = json_decode($jsonRequest, true);
        $this->assertArrayHasKey('calculation_id', $arr);
        $this->assertArrayHasKey('model_id', $arr);
        $this->assertEquals($modelId->toString(), $arr['model_id']);

        $this->assertArrayHasKey('type', $arr);
        $this->assertEquals('flopy_calculation', $arr['type']);

        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'dis'));
        $dis = $this->getPackageData($arr, 'dis');
        $this->assertArrayHasKey('top', $dis);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_steady_calculation_from_model_with_two_well_boundaries(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $wellBoundary = WellBoundary::createWithParams(
            Name::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.671125, -31.325009, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL))
        );

        /** @var WellBoundary $wellBoundary */
        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), -5000));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        $wellBoundary = WellBoundary::createWithParams(
            Name::fromString('Test Well 2'),
            Geometry::fromPoint(new Point(-63.659952, -31.330144, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), -2000));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'wel'));

        $wel = $this->getPackageData($arr, 'wel');
        $this->assertArrayHasKey('stress_period_data', $wel);
        $stressperiodData = $wel['stress_period_data'];
        $this->assertCount(1, $stressperiodData);
        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(2, $dataForFirstStressPeriod);
        $this->assertContains([0, 12, 17, -2000], $dataForFirstStressPeriod);
        $this->assertContains([0, 8, 10, -5000], $dataForFirstStressPeriod);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_steady_calculation_from_model_with_wells_and_head_observations(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $wellBoundary = WellBoundary::createWithParams(
            Name::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.671125, -31.325009, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL))
        );

        /** @var WellBoundary $wellBoundary */
        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), -5000));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        $wellBoundary = WellBoundary::createWithParams(
            Name::fromString('Test Well 2'),
            Geometry::fromPoint(new Point(-63.659952, -31.330144, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), -2000));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        /** @var HeadObservationWell $headObservation */
        $headObservation = HeadObservationWell::createWithParams(
            Name::fromString('Hob Well 1'),
            Geometry::fromPoint(new Point(-63.66, -31.34, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()
        );

        $headObservation->addHeadObservation(
            HeadObservationWellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), 100)
        );

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $headObservation));

        /** @var HeadObservationWell $headObservation */
        $headObservation = HeadObservationWell::createWithParams(
            Name::fromString('Hob Well 2'),
            Geometry::fromPoint(new Point(-63.60, -31.35, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()
        );

        $headObservation->addHeadObservation(
            HeadObservationWellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), 120)
        );

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $headObservation));
        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'hob'));

        $hob = $this->getPackageData($arr, 'hob');
        $this->assertEquals(1051, $hob['iuhobsv']);
        $this->assertEquals(0, $hob['hobdry']);
        $this->assertEquals(1, $hob['tomulth']);
        $this->assertEquals('hob', $hob['extension']);
        $this->assertEquals(null, $hob['unitnumber']);

        $obsData = $hob['obs_data'];
        $this->assertCount(2, $obsData);

        $obs1 = $obsData[0];
        $this->assertEquals(1, $obs1['tomulth']);
        $this->assertEquals('Hob Well 1', $obs1['obsname']);
        $this->assertEquals(0, $obs1['layer']);
        $this->assertEquals(19, $obs1['row']);
        $this->assertEquals(17, $obs1['column']);
        $this->assertEquals(null, $obs1['irefsp']);
        $this->assertEquals(0, $obs1['roff']);
        $this->assertEquals(0, $obs1['coff']);
        $this->assertEquals(1, $obs1['itt']);
        $this->assertEquals([[0, 100]], $obs1['time_series_data']);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function create_calculation_from_model_with_two_stress_periods_and_two_well_boundaries_on_the_same_grid_cell_should_sum_up_pumping_rates(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $wellBoundary = WellBoundary::createWithParams(
            Name::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.671125, -31.325009, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL))
        );

        /** @var WellBoundary $wellBoundary */
        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), -5000));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        $wellBoundary = WellBoundary::createWithParams(
            Name::fromString('Test Well 2'),
            Geometry::fromPoint(new Point(-63.671126, -31.325010, 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()->addWellType(WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2015-01-01')), -2000));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $wellBoundary));

        /* Create the two stressperiods */
        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-01-31'));
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);
        $stressperiods = StressPeriods::create($start, $end, $timeUnit);
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1, 1, 1, true));
        $stressperiods->addStressPeriod(StressPeriod::create(1, 100, 1, 1, false));
        $this->commandBus->dispatch(UpdateStressPeriods::of($ownerId, $modelId, $stressperiods));

        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);
        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'wel'));
        $wel = $this->getPackageData($arr, 'wel');

        $this->assertArrayHasKey('stress_period_data', $wel);
        $stressperiodData = $wel['stress_period_data'];
        $this->assertCount(2, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(1, $dataForFirstStressPeriod);
        $this->assertContains([0, 8, 10, -7000], $dataForFirstStressPeriod);

        $dataForSecondStressPeriod = array_values($stressperiodData)[1];
        $this->assertCount(1, $dataForSecondStressPeriod);
        $this->assertContains([0, 8, 10, -7000], $dataForSecondStressPeriod);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function create_steady_calculation_from_model_with_chd_boundary_with_one_observationpoint(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $chdBoundary = $this->createConstantHeadBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $chdBoundary));

        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $chdBoundary->boundaryId());
        $numberOfAffectedCells = \count($affectedCells->cells());

        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'chd'));
        $chd = $this->getPackageData($arr, 'chd');
        $this->assertArrayHasKey('stress_period_data', $chd);
        $stressperiodData = $chd['stress_period_data'];
        $this->assertCount(1, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount($numberOfAffectedCells, $dataForFirstStressPeriod);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_steady_calculation_from_model_with_ghb_boundary_with_one_observationpoint(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $ghbBoundary = $this->createGeneralHeadBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $ghbBoundary));

        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $ghbBoundary->boundaryId());
        $numberOfAffectedCells = \count($affectedCells->cells());

        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'ghb'));
        $ghb = $this->getPackageData($arr, 'ghb');

        $this->assertArrayHasKey('stress_period_data', $ghb);
        $stressperiodData = $ghb['stress_period_data'];
        $this->assertCount(1, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount($numberOfAffectedCells, $dataForFirstStressPeriod);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_steady_calculation_from_model_with_rch_boundary(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $rchBoundary = $this->createRechargeBoundaryCenter();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $rchBoundary));
        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'rch'));
        $rch = $this->getPackageData($arr, 'rch');

        $this->assertArrayHasKey('stress_period_data', $rch);
        $stressperiodData = $rch['stress_period_data'];
        $this->assertCount(1, $stressperiodData);
        $stressperiodDataFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(40, $stressperiodDataFirstStressPeriod);
        $this->assertCount(75, $stressperiodDataFirstStressPeriod[0]);
        $this->assertEquals(0.000329, $stressperiodDataFirstStressPeriod[27][30]);
    }

    /**
     * @test
     * @throws \Exception
     * @throws \Exception
     */
    public function it_creates_a_steady_calculation_from_model_with_two_overlapping_rch_boundaries(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $rchBoundary = $this->createRechargeBoundaryCenter();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $rchBoundary));

        $rchBoundary = $this->createRechargeBoundaryLower();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $rchBoundary));
        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'rch'));
        $rch = $this->getPackageData($arr, 'rch');

        $this->assertArrayHasKey('stress_period_data', $rch);
        $stressperiodData = $rch['stress_period_data'];
        $this->assertCount(1, $stressperiodData);
        $stressperiodDataFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount(40, $stressperiodDataFirstStressPeriod);
        $this->assertCount(75, $stressperiodDataFirstStressPeriod[0]);
        $this->assertEquals(0.000529, $stressperiodDataFirstStressPeriod[27][30]);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_steady_calculation_from_model_with_riv_boundary_with_one_observationpoint(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $riverBoundary = $this->createRiverBoundaryWithObservationPoint();
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $riverBoundary));
        $config = $this->recalculateAndCreateJsonCalculationRequest($modelId);

        $this->assertJson($config);
        $arr = json_decode($config, true);
        $this->assertTrue($this->packageIsInSelectedPackages($arr, 'riv'));
        $riv = $this->getPackageData($arr, 'riv');

        $affectedCells = $this->container->get('inowas.modflowmodel.manager')->getBoundaryAffectedCells($modelId, $riverBoundary->boundaryId());
        $numberOfAffectedCells = \count($affectedCells->cells());

        $this->assertArrayHasKey('stress_period_data', $riv);
        $stressperiodData = $riv['stress_period_data'];
        $this->assertCount(1, $stressperiodData);

        $dataForFirstStressPeriod = array_values($stressperiodData)[0];
        $this->assertCount($numberOfAffectedCells, $dataForFirstStressPeriod);
    }

    /**
     * @test
     * @throws \exception
     */
    public function it_updates_calculation_packages_lpf_laytyp(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->container->get('inowas.modflowmodel.modflow_packages_manager')->recalculate($modelId);
        $this->commandBus->dispatch(UpdateModflowPackageParameter::byUserModelIdAndPackageData($ownerId, $modelId, PackageName::fromString('lpf'), ParameterName::fromString('layTyp'), Laytyp::fromArray(array(0))));

        $calculationId = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getCalculationId($modelId);
        $packages = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getPackages($calculationId);

        $this->assertTrue($packages->isSelected(PackageName::fromString('lpf')));

        $mfPackages = json_decode(json_encode($packages), true)['mf'];
        $this->assertArrayHasKey('lpf', $mfPackages);
        $this->assertArrayHasKey('laytyp', $mfPackages['lpf']);
        $this->assertEquals([0], $mfPackages['lpf']['laytyp']);
    }

    /**
     * @test
     * @throws \exception
     */
    public function it_updates_calculation_packages_lpf_laywet(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->container->get('inowas.modflowmodel.modflow_packages_manager')->recalculate($modelId);

        $this->commandBus->dispatch(UpdateModflowPackageParameter::byUserModelIdAndPackageData($ownerId, $modelId, PackageName::fromString('lpf'), ParameterName::fromString('layWet'), Laywet::fromArray(array(1))));

        $calculationId = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getCalculationId($modelId);
        $packages = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getPackages($calculationId);
        $this->assertTrue($packages->isSelected(PackageName::fromString('lpf')));

        $mfPackages = json_decode(json_encode($packages), true)['mf'];
        $this->assertArrayHasKey('lpf', $mfPackages);
        $this->assertArrayHasKey('laywet', $mfPackages['lpf']);
        $this->assertEquals([1], $mfPackages['lpf']['laywet']);
    }

    /**
     * @test
     * @throws \exception
     */
    public function it_can_change_flow_package_to_upw(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->container->get('inowas.modflowmodel.modflow_packages_manager')->recalculate($modelId);

        $this->commandBus->dispatch(ChangeFlowPackage::forModflowModel($ownerId, $modelId, PackageName::fromString('upw')));

        $calculationId = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getCalculationId($modelId);
        $packages = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getPackages($calculationId);
        $this->assertTrue($packages->isSelected(PackageName::fromString('upw')));

        $mfPackages = json_decode(json_encode($packages), true)['mf'];
        $this->assertArrayHasKey('upw', $mfPackages);
    }

    /**
     * @test
     * @throws \exception
     */
    public function it_can_change_calculation_package_mf_version(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->container->get('inowas.modflowmodel.modflow_packages_manager')->recalculate($modelId);

        $this->commandBus->dispatch(UpdateModflowPackageParameter::byUserModelIdAndPackageData(
            $ownerId,
            $modelId,
            PackageName::fromString('mf'),
            ParameterName::fromString('version'),
            Version::fromString('mfnwt')
        ));

        $calculationId = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getCalculationId($modelId);
        $packages = $this->container->get('inowas.modflowmodel.modflow_packages_manager')->getPackages($calculationId);
        $this->assertTrue($packages->isSelected(PackageName::fromString('mf')));

        $mfPackages = json_decode(json_encode($packages), true)['mf'];
        $this->assertArrayHasKey('mf', $mfPackages);
        $this->assertEquals('mfnwt', $mfPackages['mf']['version']);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_clones_a_modflow_model_and_all_boundaries(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->assertCount(1, $this->container->get('inowas.modflowmodel.model_finder')->findAll());

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRechargeBoundaryCenter()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createWellBoundary()));

        $newModelId = ModflowId::generate();
        $this->commandBus->dispatch(CloneModflowModel::byId($modelId, $ownerId, $newModelId));
        $this->assertCount(2, $this->container->get('inowas.modflowmodel.model_finder')->findAll());
        $this->assertEquals(5, $this->container->get('inowas.modflowmodel.boundary_manager')->getTotalNumberOfModelBoundaries($modelId));

        $this->assertNull($this->container->get('inowas.tool.tools_finder')->findById(ToolId::fromString($newModelId->toString())));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_clones_a_modflow_model_and_tool_and_all_boundaries(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);
        $this->assertCount(1, $this->container->get('inowas.modflowmodel.model_finder')->findAll());

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRechargeBoundaryCenter()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createWellBoundary()));

        $newModelId = ModflowId::generate();
        $this->commandBus->dispatch(CloneModflowModel::byId($modelId, $ownerId, $newModelId, true));
        $this->assertCount(2, $this->container->get('inowas.modflowmodel.model_finder')->findAll());
        $this->assertEquals(5, $this->container->get('inowas.modflowmodel.boundary_manager')->getTotalNumberOfModelBoundaries($modelId));

        $this->assertNotNull($this->container->get('inowas.tool.tools_finder')->findById(ToolId::fromString($newModelId->toString())));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_an_optimization_and_writes_to_projection(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $optimizationId = ModflowId::generate();
        $optimizationInput = OptimizationInput::fromArray(['id' => $optimizationId->toString(), '123' => 456, '789' => 111]);

        $this->commandBus->dispatch(UpdateOptimizationInput::forModflowModel($ownerId, $modelId, $optimizationInput));
        $optimizationFinder = $this->container->get('inowas.modflowmodel.optimization_finder');
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertInstanceOf(Optimization::class, $optimization);
        $this->assertEquals($optimizationInput, $optimization->input());

        $changedOptimizationInput = OptimizationInput::fromArray(['id' => $optimizationId->toString(), '456' => 456, '789' => 111]);
        $this->commandBus->dispatch(UpdateOptimizationInput::forModflowModel($ownerId, $modelId, $changedOptimizationInput));
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertInstanceOf(Optimization::class, $optimization);
        $this->assertEquals($changedOptimizationInput, $optimization->input());

        $this->commandBus->dispatch(UpdateOptimizationCalculationState::isPreprocessing($modelId, $optimizationId));
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertEquals(OptimizationState::PREPROCESSING, $optimization->state()->toInt());

        $this->commandBus->dispatch(UpdateOptimizationCalculationState::preprocessingFinished($modelId, $optimizationId, CalculationId::fromString('calcId')));
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertEquals(OptimizationState::PREPROCESSING_FINISHED, $optimization->state()->toInt());

        $this->commandBus->dispatch(UpdateOptimizationCalculationState::calculating($modelId, $optimizationId));
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertEquals(OptimizationState::CALCULATING, $optimization->state()->toInt());

        $response = ModflowOptimizationResponse::fromJson(sprintf('
            {
              "optimization_id": "%s",
              "message": "",
              "status_code": 200,
              "methods": [
                {
                  "name": "GA",
                  "solutions": [
                    {
                      "fitness": [
                        -37.682159423828125
                      ],
                      "variables": [
                        -1840.069966638935,
                        -1795.742561436709,
                        -1964.9186956262422,
                        -829.3974928390986,
                        -1660.0108681288707,
                        -1549.2325988304983,
                        -631.3082955796821,
                        -1938.0615617076314,
                        -1905.6267269118298,
                        -1998.8687160951977,
                        -962.0652533729562,
                        -1758.8053320732906,
                        -826.0204161649331
                      ],
                      "objects": [
                        {
                          "id": "d0e5ac92-de5d-46aa-811d-497ef2178fbc",
                          "name": "New Optimization Object",
                          "type": "wel",
                          "position": {
                            "lay": {
                              "min": 1,
                              "max": 1,
                              "result": 1
                            },
                            "row": {
                              "min": 35,
                              "max": 35,
                              "result": 35
                            },
                            "col": {
                              "min": 30,
                              "max": 30,
                              "result": 30
                            }
                          },
                          "flux": {
                            "0": {
                              "min": -2000,
                              "max": 0,
                              "result": -1840.0510176859432
                            },
                            "1": {
                              "min": -2000,
                              "max": 0,
                              "result": -215.05437520846567
                            },
                            "2": {
                              "min": -2000,
                              "max": 0,
                              "result": -1943.1049875305832
                            },
                            "3": {
                              "min": -2000,
                              "max": 0,
                              "result": -1904.7920187328527
                            },
                            "4": {
                              "min": -2000,
                              "max": 0,
                              "result": -1660.0095289753706
                            },
                            "5": {
                              "min": -2000,
                              "max": 0,
                              "result": -1549.2307834348953
                            },
                            "6": {
                              "min": -2000,
                              "max": 0,
                              "result": -631.3064030891087
                            },
                            "7": {
                              "min": -2000,
                              "max": 0,
                              "result": -1940.713658548958
                            },
                            "8": {
                              "min": -2000,
                              "max": 0,
                              "result": -1475.0584439519534
                            },
                            "9": {
                              "min": -2000,
                              "max": 0,
                              "result": -1895.031452841381
                            },
                            "10": {
                              "min": -2000,
                              "max": 0,
                              "result": -962.0652533729562
                            },
                            "11": {
                              "min": -2000,
                              "max": 0,
                              "result": -1758.8858623797203
                            },
                            "12": {
                              "min": -2000,
                              "max": 0,
                              "result": -818.1245195487583
                            }
                          },
                          "concentration": {},
                          "substances": [],
                          "numberOfStressPeriods": 13
                        }
                      ]
                    }
                  ],
                  "progress": {
                    "progress_log": [
                      0.9547843933105469,
                      0.9547843933105469,
                      0.9572219848632812,
                      1.0946731567382812,
                      1.26666259765625,
                      1.26666259765625,
                      1.3035430908203125,
                      1.4080619812011719,
                      1.412384033203125,
                      1.4176406860351562
                    ],
                    "simulation": 10,
                    "simulation_total": 10,
                    "iteration": 10,
                    "iteration_total": 10,
                    "final": true
                  }
                }
              ]
            }', $optimizationId->toString()));
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::calculatingWithProgressUpdate($modelId, $response));
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertEquals(OptimizationState::FINISHED, $optimization->state()->toInt());
        $methods = $optimization->methods()->toArray();
        $this->assertCount(1, $methods);

        /** @var OptimizationMethod $method */
        $method = OptimizationMethod::fromArray($methods[0]);
        $this->assertCount(6, $method->progress()->toArray());
        $this->assertCount(1, $method->solutions()->toArray());

        $this->assertEquals('GA', $method->name());
        $this->assertEquals([
            'progress_log' => [0.9547843933105469, 0.9547843933105469, 0.9572219848632812, 1.0946731567382812, 1.26666259765625, 1.26666259765625, 1.3035430908203125, 1.4080619812011719, 1.412384033203125, 1.4176406860351562],
            'simulation' => 10,
            'simulation_total' => 10,
            'iteration' => 10,
            'iteration_total' => 10,
            'final' => true
        ], $method->progress()->toArray());
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::cancelled($modelId, $optimizationId));
        $optimization = $optimizationFinder->getOptimizationByModelId($modelId);
        $this->assertEquals(OptimizationState::CANCELLED, $optimization->state()->toInt());
        $response2 = ModflowOptimizationResponse::fromJson(sprintf('
            {"status_code": "202", "message": "Received \"optimization_start\" request. Staring workers...", "optimization_id": "%s"}', $optimizationId->toString()));
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::calculatingWithProgressUpdate($modelId, $response2));
        $response3 = ModflowOptimizationResponse::fromJson(sprintf('
            {"status_code": "200", "message": "Warning. Could not stop workers. \'0ac65f86-6750-402e-9aa1-f4021ba73233\'\r\n", "optimization_id": "%s"}', $optimizationId->toString()));
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::calculatingWithProgressUpdate($modelId, $response3));
        $response = ModflowOptimizationResponse::fromJson(sprintf('
            {"optimization_id": "%s", "status_code": 200, "message": "", "methods": [{"name": "GA", "progress": {"progress_log": [0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625], "simulation": 10, "simulation_total": 10, "iteration": 10, "iteration_total": 10, "final": true}, "solutions": [{"id": "9e2c31fe-024c-49be-8d80-4b482397c135", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [15.456262776048202, 0.08953699859222697, 29.249716838956452, 851.8029060975591, 237.23635573046158, 94.56802196634658, 150.89073726969036, 27.20270404624219, 799, 109.11813007821809, 22.838266354240183, 340.2692250447142, 404.4960463779422, 625, 714], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 15}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 29.249716838956452}, "1": {"min": 0, "max": 1000, "result": 851.8029060975591}, "2": {"min": 0, "max": 1000, "result": 237.23635573046158}, "3": {"min": 0, "max": 1000, "result": 94.56802196634658}, "4": {"min": 0, "max": 1000, "result": 150.89073726969036}, "5": {"min": 0, "max": 1000, "result": 27.20270404624219}, "6": {"min": 0, "max": 999, "result": 799}, "7": {"min": 0, "max": 1000, "result": 109.11813007821809}, "8": {"min": 0, "max": 100, "result": 22.838266354240183}, "9": {"min": 0, "max": 1000, "result": 340.2692250447142}, "10": {"min": 0, "max": 1000, "result": 404.4960463779422}, "11": {"min": 0, "max": 1000, "result": 625}, "12": {"min": 0, "max": 1000, "result": 714}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "cbe505a9-1812-47c9-8556-17ad2205e2a0", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [16.244187567066923, 0.08953699859222697, 23.052311103209355, 450, 428, 93.87438571480607, 150.89073726969036, 31.169697567503192, 799.5637176356232, 109.11813007821809, 21.958870796563847, 340.2692250447142, 404.4960463779422, 58.04675176511893, 714], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 16}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 23.052311103209355}, "1": {"min": 0, "max": 1000, "result": 450}, "2": {"min": 0, "max": 1000, "result": 428}, "3": {"min": 0, "max": 1000, "result": 93.87438571480607}, "4": {"min": 0, "max": 1000, "result": 150.89073726969036}, "5": {"min": 0, "max": 1000, "result": 31.169697567503192}, "6": {"min": 0, "max": 999, "result": 799.5637176356232}, "7": {"min": 0, "max": 1000, "result": 109.11813007821809}, "8": {"min": 0, "max": 100, "result": 21.958870796563847}, "9": {"min": 0, "max": 1000, "result": 340.2692250447142}, "10": {"min": 0, "max": 1000, "result": 404.4960463779422}, "11": {"min": 0, "max": 1000, "result": 58.04675176511893}, "12": {"min": 0, "max": 1000, "result": 714}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "15e8e38b-fb77-4b94-a6fa-8b879e4d9b1f", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [24, 0, 579, 593, 258, 77.8917183760584, 543.6808714335601, 621.9675628766367, 555.2403369695377, 886.8881597873005, 40, 284, 398.9751851122995, 626.8929690251958, 424.16060890109634], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 24}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 579}, "1": {"min": 0, "max": 1000, "result": 593}, "2": {"min": 0, "max": 1000, "result": 258}, "3": {"min": 0, "max": 1000, "result": 77.8917183760584}, "4": {"min": 0, "max": 1000, "result": 543.6808714335601}, "5": {"min": 0, "max": 1000, "result": 621.9675628766367}, "6": {"min": 0, "max": 999, "result": 555.2403369695377}, "7": {"min": 0, "max": 1000, "result": 886.8881597873005}, "8": {"min": 0, "max": 100, "result": 40}, "9": {"min": 0, "max": 1000, "result": 284}, "10": {"min": 0, "max": 1000, "result": 398.9751851122995}, "11": {"min": 0, "max": 1000, "result": 626.8929690251958}, "12": {"min": 0, "max": 1000, "result": 424.16060890109634}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "d23f8c0d-7edb-4463-b9f4-967c451b129a", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [16.128614238120186, 0.0538616768800384, 25.533457697061493, 451.6874939284661, 428, 835.9478197690665, 158.1816673364854, 31.169697567503192, 800.5252013917412, 109.11813007821809, 40.91650005032182, 340.2692250447142, 404.4960463779422, 616.114735532399, 711.3594345829708], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 16}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 25.533457697061493}, "1": {"min": 0, "max": 1000, "result": 451.6874939284661}, "2": {"min": 0, "max": 1000, "result": 428}, "3": {"min": 0, "max": 1000, "result": 835.9478197690665}, "4": {"min": 0, "max": 1000, "result": 158.1816673364854}, "5": {"min": 0, "max": 1000, "result": 31.169697567503192}, "6": {"min": 0, "max": 999, "result": 800.5252013917412}, "7": {"min": 0, "max": 1000, "result": 109.11813007821809}, "8": {"min": 0, "max": 100, "result": 40.91650005032182}, "9": {"min": 0, "max": 1000, "result": 340.2692250447142}, "10": {"min": 0, "max": 1000, "result": 404.4960463779422}, "11": {"min": 0, "max": 1000, "result": 616.114735532399}, "12": {"min": 0, "max": 1000, "result": 711.3594345829708}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "2409542d-6579-4c9f-9207-ad69a1347b85", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [2.991770434012224, 0.04824881545830806, 29.299344523790115, 819.363779105621, 235.26826576247697, 813.5021018631619, 697.7969942066144, 27.22793721558412, 804.9021008371761, 104.3689300380965, 22.845088423780002, 902, 787.8363903808768, 614.4317284454061, 418.67454087789315], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 2}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 29.299344523790115}, "1": {"min": 0, "max": 1000, "result": 819.363779105621}, "2": {"min": 0, "max": 1000, "result": 235.26826576247697}, "3": {"min": 0, "max": 1000, "result": 813.5021018631619}, "4": {"min": 0, "max": 1000, "result": 697.7969942066144}, "5": {"min": 0, "max": 1000, "result": 27.22793721558412}, "6": {"min": 0, "max": 999, "result": 804.9021008371761}, "7": {"min": 0, "max": 1000, "result": 104.3689300380965}, "8": {"min": 0, "max": 100, "result": 22.845088423780002}, "9": {"min": 0, "max": 1000, "result": 902}, "10": {"min": 0, "max": 1000, "result": 787.8363903808768}, "11": {"min": 0, "max": 1000, "result": 614.4317284454061}, "12": {"min": 0, "max": 1000, "result": 418.67454087789315}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "7fbfad4d-5f1e-45e0-aad3-da237d61c64e", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [15.977063147766444, 0.7282080532018096, 194.8707966760602, 820.5818837040761, 196.60606459627033, 160.6672067299948, 535.4496838239402, 530.2767923554985, 565, 107, 41.031766979878476, 286.0565063717235, 537.8581665671126, 615.8920208173035, 553.8769445163921], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 15}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 194.8707966760602}, "1": {"min": 0, "max": 1000, "result": 820.5818837040761}, "2": {"min": 0, "max": 1000, "result": 196.60606459627033}, "3": {"min": 0, "max": 1000, "result": 160.6672067299948}, "4": {"min": 0, "max": 1000, "result": 535.4496838239402}, "5": {"min": 0, "max": 1000, "result": 530.2767923554985}, "6": {"min": 0, "max": 999, "result": 565}, "7": {"min": 0, "max": 1000, "result": 107}, "8": {"min": 0, "max": 100, "result": 41.031766979878476}, "9": {"min": 0, "max": 1000, "result": 286.0565063717235}, "10": {"min": 0, "max": 1000, "result": 537.8581665671126}, "11": {"min": 0, "max": 1000, "result": 615.8920208173035}, "12": {"min": 0, "max": 1000, "result": 553.8769445163921}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "f3892aef-af4e-45e7-8104-d669c41a7ced", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [16.128614238120186, 0.08953699859222697, 23.034352484832993, 450, 428, 93.87438571480607, 150.89073726969036, 31.169697567503192, 799, 109.11813007821809, 22.0130257634194, 340.2692250447142, 404.4960463779422, 625, 714], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 16}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 23.034352484832993}, "1": {"min": 0, "max": 1000, "result": 450}, "2": {"min": 0, "max": 1000, "result": 428}, "3": {"min": 0, "max": 1000, "result": 93.87438571480607}, "4": {"min": 0, "max": 1000, "result": 150.89073726969036}, "5": {"min": 0, "max": 1000, "result": 31.169697567503192}, "6": {"min": 0, "max": 999, "result": 799}, "7": {"min": 0, "max": 1000, "result": 109.11813007821809}, "8": {"min": 0, "max": 100, "result": 22.0130257634194}, "9": {"min": 0, "max": 1000, "result": 340.2692250447142}, "10": {"min": 0, "max": 1000, "result": 404.4960463779422}, "11": {"min": 0, "max": 1000, "result": 625}, "12": {"min": 0, "max": 1000, "result": 714}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "88627007-d1d5-43d8-a30d-0f3b9b2911c0", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [23.47721021852438, 0.7282080532018096, 194.8707966760602, 593, 205.6537713903021, 831.1382150987306, 535.4496838239402, 530.2767923554985, 565, 107, 41.031766979878476, 284, 537.8581665671126, 616.0956644523842, 553.8769445163921], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 23}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 194.8707966760602}, "1": {"min": 0, "max": 1000, "result": 593}, "2": {"min": 0, "max": 1000, "result": 205.6537713903021}, "3": {"min": 0, "max": 1000, "result": 831.1382150987306}, "4": {"min": 0, "max": 1000, "result": 535.4496838239402}, "5": {"min": 0, "max": 1000, "result": 530.2767923554985}, "6": {"min": 0, "max": 999, "result": 565}, "7": {"min": 0, "max": 1000, "result": 107}, "8": {"min": 0, "max": 100, "result": 41.031766979878476}, "9": {"min": 0, "max": 1000, "result": 284}, "10": {"min": 0, "max": 1000, "result": 537.8581665671126}, "11": {"min": 0, "max": 1000, "result": 616.0956644523842}, "12": {"min": 0, "max": 1000, "result": 553.8769445163921}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "c415e268-bdd5-43f1-9d27-6c23179b0880", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [16.13353354871235, 0.08953699859222697, 94, 450, 428, 818.4945576509139, 144.15979551154112, 18, 799, 109.11813007821809, 4, 340.2692250447142, 404.4960463779422, 625, 714], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 16}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 94}, "1": {"min": 0, "max": 1000, "result": 450}, "2": {"min": 0, "max": 1000, "result": 428}, "3": {"min": 0, "max": 1000, "result": 818.4945576509139}, "4": {"min": 0, "max": 1000, "result": 144.15979551154112}, "5": {"min": 0, "max": 1000, "result": 18}, "6": {"min": 0, "max": 999, "result": 799}, "7": {"min": 0, "max": 1000, "result": 109.11813007821809}, "8": {"min": 0, "max": 100, "result": 4}, "9": {"min": 0, "max": 1000, "result": 340.2692250447142}, "10": {"min": 0, "max": 1000, "result": 404.4960463779422}, "11": {"min": 0, "max": 1000, "result": 625}, "12": {"min": 0, "max": 1000, "result": 714}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}, {"id": "07abe815-d09a-4e6c-9163-8eb410bb790c", "locally_optimized": false, "fitness": [453.1766357421875], "variables": [24, 0, 579, 593, 258, 853, 543, 153, 565, 107, 40, 284, 397, 748, 881], "objects": [{"id": "d482627c-6019-497e-9b88-e745764046b1", "name": "Well 1", "type": "wel", "position": {"lay": {"min": 0, "max": 0, "result": 0}, "row": {"min": 0, "max": 28, "result": 24}, "col": {"min": 0, "max": 33, "result": 0}}, "flux": {"0": {"min": 0, "max": 1000, "result": 579}, "1": {"min": 0, "max": 1000, "result": 593}, "2": {"min": 0, "max": 1000, "result": 258}, "3": {"min": 0, "max": 1000, "result": 853}, "4": {"min": 0, "max": 1000, "result": 543}, "5": {"min": 0, "max": 1000, "result": 153}, "6": {"min": 0, "max": 999, "result": 565}, "7": {"min": 0, "max": 1000, "result": 107}, "8": {"min": 0, "max": 100, "result": 40}, "9": {"min": 0, "max": 1000, "result": 284}, "10": {"min": 0, "max": 1000, "result": 397}, "11": {"min": 0, "max": 1000, "result": 748}, "12": {"min": 0, "max": 1000, "result": 881}}, "concentration": {}, "substances": [], "numberOfStressPeriods": 13}]}]}]}
        ', $optimizationId->toString()));
        $this->commandBus->dispatch(UpdateOptimizationCalculationState::calculatingWithProgressUpdate($modelId, $response));
        $optimization = $optimizationFinder->getOptimizationById($optimizationId);
        $this->assertEquals([0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625, 0.2265625], $optimization->methods()->toArray()[0]['progress']['progress_log']);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_public_scenarioanalysis_from_a_basemodel(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->createScenarioAnalysis(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription'),
            Visibility::public()
        );

        $scenarioAnalysis = $this->container->get('inowas.scenarioanalysis.scenarioanalysis_finder')->findScenarioAnalysisDetailsById($scenarioAnalysisId);
        $this->assertEquals($scenarioAnalysisId->toString(), $scenarioAnalysis['id']);
        $this->assertEquals($ownerId->toString(), $scenarioAnalysis['user_id']);
        $this->assertEquals('TestName', $scenarioAnalysis['name']);
        $this->assertEquals('TestDescription', $scenarioAnalysis['description']);
        $this->assertEquals(json_decode('{"type":"Polygon","coordinates":[[[-63.687336,-31.313615],[-63.687336,-31.367449],[-63.56926,-31.367449],[-63.56926,-31.313615],[-63.687336,-31.313615]]]}', true), $scenarioAnalysis['geometry']);
        $this->assertEquals(json_decode('{"n_x":75,"n_y":40}', true), $scenarioAnalysis['grid_size']);

        $expectedBb = array(
            [-63.687336, -31.367449],
            [-63.56926, -31.313615]
        );

        $this->assertEquals($expectedBb, $scenarioAnalysis['bounding_box']);
        $this->assertTrue($this->container->get('inowas.scenarioanalysis.scenarioanalysis_finder')->isPublic($scenarioAnalysisId));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_creates_a_private_scenarioanalysis_from_a_basemodel(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithOneLayer($ownerId, $modelId);

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->createScenarioAnalysis(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription'),
            Visibility::private()
        );

        $scenarioAnalysis = $this->container->get('inowas.scenarioanalysis.scenarioanalysis_finder')->findScenarioAnalysisDetailsById($scenarioAnalysisId);
        $this->assertEquals($scenarioAnalysisId->toString(), $scenarioAnalysis['id']);
        $this->assertEquals($ownerId->toString(), $scenarioAnalysis['user_id']);
        $this->assertEquals('TestName', $scenarioAnalysis['name']);
        $this->assertEquals('TestDescription', $scenarioAnalysis['description']);
        $this->assertEquals(json_decode('{"type":"Polygon","coordinates":[[[-63.687336,-31.313615],[-63.687336,-31.367449],[-63.56926,-31.367449],[-63.56926,-31.313615],[-63.687336,-31.313615]]]}', true), $scenarioAnalysis['geometry']);
        $this->assertEquals(json_decode('{"n_x":75,"n_y":40}', true), $scenarioAnalysis['grid_size']);

        $expectedBb = array(
            [-63.687336, -31.367449],
            [-63.56926, -31.313615]
        );

        $this->assertEquals($expectedBb, $scenarioAnalysis['bounding_box']);
        $this->assertFalse($this->container->get('inowas.scenarioanalysis.scenarioanalysis_finder')->isPublic($scenarioAnalysisId));
    }

    /**
     * @test
     * @throws \Exception
     */
    public function create_scenarioanalysis_from_basemodel_with_all_boundary_types(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRechargeBoundaryCenter()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createWellBoundary()));

        $baseModelBoundaries = $this->container->get('inowas.modflowmodel.boundary_manager')->findBoundariesByModelId($modelId);
        $this->assertCount(5, $baseModelBoundaries);

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->createScenarioAnalysis(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription'),
            Visibility::public()
        );

        $scenarioAnalysis = $this->container->get('inowas.scenarioanalysis.scenarioanalysis_finder')->findScenarioAnalysisDetailsById($scenarioAnalysisId);
        $this->assertEquals($scenarioAnalysisId->toString(), $scenarioAnalysis['id']);
        $this->assertEquals($ownerId->toString(), $scenarioAnalysis['user_id']);
        $this->assertEquals('TestName', $scenarioAnalysis['name']);
        $this->assertEquals('TestDescription', $scenarioAnalysis['description']);
        $this->assertEquals(json_decode('{"type":"Polygon","coordinates":[[[-63.65,-31.31],[-63.65,-31.36],[-63.58,-31.36],[-63.58,-31.31],[-63.65,-31.31]]]}', true), $scenarioAnalysis['geometry']);
        $this->assertEquals(json_decode('{"n_x":75,"n_y":40}', true), $scenarioAnalysis['grid_size']);

        $expectedBb = array(
            [-63.65, -31.36],
            [-63.58, -31.31]
        );

        $this->assertEquals($expectedBb, $scenarioAnalysis['bounding_box']);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function add_well_to_scenario_from_basemodel_with_all_other_boundary_types(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRechargeBoundaryCenter()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));
        $modelBoundaries = $this->container->get('inowas.modflowmodel.boundary_manager')->findBoundariesByModelId($modelId);
        $this->assertCount(4, $modelBoundaries);

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->createScenarioAnalysis(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription'),
            Visibility::public()
        );

        $scenarioId = ModflowId::generate();
        $this->createScenario($scenarioAnalysisId, $ownerId, $modelId, $scenarioId, Name::fromString('TestScenarioName'), Description::fromString('TestScenarioDescription'));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $scenarioId, $this->createWellBoundary()));
        $scenarioBoundaries = $this->container->get('inowas.modflowmodel.boundary_manager')->findBoundariesByModelId($scenarioId);
        $this->assertCount(5, $scenarioBoundaries);
    }

    /**
     * @test
     * @throws \Exception
     */
    public function it_can_move_well_of_scenario_from_basemodel_with_all_boundary_types(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->createModelWithName($ownerId, $modelId);

        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $well = $this->createWellBoundary()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createConstantHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createGeneralHeadBoundaryWithObservationPoint()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRechargeBoundaryCenter()));
        $this->commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $modelId, $this->createRiverBoundaryWithObservationPoint()));

        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->createScenarioAnalysis(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('TestName'),
            ScenarioAnalysisDescription::fromString('TestDescription'),
            Visibility::public()
        );

        $scenarioId = ModflowId::generate();
        $this->createScenario($scenarioAnalysisId, $ownerId, $modelId, $scenarioId, Name::fromString('TestScenarioName'), Description::fromString('TestScenarioDescription'));

        $newGeometry = Geometry::fromPoint(new Point(-63.6, -31.32, 4326));
        $updatedWell = $well->updateGeometry($newGeometry);

        $this->commandBus->dispatch(UpdateBoundary::forModflowModel($ownerId, $scenarioId, $updatedWell->boundaryId(), $updatedWell));
        $scenarioBoundaries = $this->container->get('inowas.modflowmodel.boundary_manager')->findBoundariesByModelId($scenarioId);
        $this->assertCount(5, $scenarioBoundaries);

        /** @var WellBoundary[] $wells */
        $wells = $this->container->get('inowas.modflowmodel.boundary_manager')->findWellBoundaries($scenarioId);
        $this->assertCount(1, $wells);

        $well = $wells[0];
        $this->assertEquals($newGeometry, $well->geometry());

        $observationPoints = $well->observationPoints();
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $observationPoints->toArrayValues()[0];
        $this->assertEquals($newGeometry, Geometry::fromPoint($observationPoint->geometry()));
    }

    /**
     * @param array $request
     * @param $packageName
     * @return bool
     */
    private function packageIsInSelectedPackages(array $request, $packageName): bool
    {
        return \array_key_exists($packageName, $request['data']['mf']);
    }

    /**
     * @param array $request
     * @param $packageName
     * @return array
     */
    private function getPackageData(array $request, $packageName): array
    {
        return $request['data']['mf'][$packageName];
    }
}
