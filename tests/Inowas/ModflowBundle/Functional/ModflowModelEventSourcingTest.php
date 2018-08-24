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
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Laywet;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\ModflowModel;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Modflow\Optimization;
use Inowas\Common\Modflow\OptimizationInput;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\ParameterName;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Modflow\Version;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\AddLayer;
use Inowas\ModflowModel\Model\Command\CalculateOptimization;
use Inowas\ModflowModel\Model\Command\CancelOptimizationCalculation;
use Inowas\ModflowModel\Model\Command\ChangeBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeFlowPackage;
use Inowas\ModflowModel\Model\Command\ChangeGridSize;
use Inowas\ModflowModel\Model\Command\CloneModflowModel;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\Command\RemoveBoundary;
use Inowas\ModflowModel\Model\Command\UpdateBoundary;
use Inowas\ModflowModel\Model\Command\UpdateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateModflowPackageParameter;
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
        $optimization = $optimizationFinder->getOptimization($modelId);
        $this->assertInstanceOf(Optimization::class, $optimization);
        $this->assertEquals($optimizationInput, $optimization->input());

        $changedOptimizationInput = OptimizationInput::fromArray(['id' => $optimizationId->toString(), '456' => 456, '789' => 111]);
        $this->commandBus->dispatch(UpdateOptimizationInput::forModflowModel($ownerId, $modelId, $changedOptimizationInput));
        $optimization = $optimizationFinder->getOptimization($modelId);
        $this->assertInstanceOf(Optimization::class, $optimization);
        $this->assertEquals($changedOptimizationInput, $optimization->input());

        $this->commandBus->dispatch(CalculateOptimization::forModflowModel($ownerId, $modelId, $optimizationId));
        $optimization = $optimizationFinder->getOptimization($modelId);
        $this->assertEquals(OptimizationState::STARTED, $optimization->state()->toInt());

        $this->commandBus->dispatch(CancelOptimizationCalculation::forModflowModel($ownerId, $modelId, $optimizationId));
        $optimization = $optimizationFinder->getOptimization($modelId);
        $this->assertEquals(OptimizationState::CANCELLED, $optimization->state()->toInt());
    }

    /**
     * @test
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
