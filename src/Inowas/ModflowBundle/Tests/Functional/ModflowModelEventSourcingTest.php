<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Tests\Functional;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RechargeDateTimeValue;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Boundaries\AreaBoundary;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Command\AddBoundary;
use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Command\ChangeModflowModelGridSize;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\ModflowModelAggregate;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Id\UserId;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;

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
        $this->create_model_with_name_bounding_box_and_grid_size($ownerId, $modelId);

        $areaId = BoundaryId::generate();
        $area = AreaBoundary::create($areaId);
        $area = $area->setName(BoundaryName::fromString('Rio Primero Area'));
        $area = $area->setGeometry(Geometry::fromPolygon(new Polygon(
            array(
                array(
                    array(-63.65, -31.31),
                    array(-63.65, -31.36),
                    array(-63.58, -31.36),
                    array(-63.58, -31.31),
                    array(-63.65, -31.31)
                )
            ), 4326
        )));

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $area));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findAreaActiveCells($modelId);
        $this->assertCount(1610, $activeCells->cells());

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $areaId);
        $this->assertCount(1610, $activeCells->cells());
    }

    public function test_add_wel_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->create_model_with_name_bounding_box_and_grid_size($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $wellBoundary = WellBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.671125, -31.325009, 4326)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            LayerNumber::fromInteger(0)
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(new \DateTimeImmutable('2015-01-01'), -5000));
        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $wellBoundary));

        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $boundaryId);
        $this->assertCount(1, $activeCells->cells());
        $this->assertEquals([[0, 8, 10]], $activeCells->cells());
    }

    public function test_add_riv_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->create_model_with_name_bounding_box_and_grid_size($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $riverBoundary = RiverBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestRiver'),
            Geometry::fromLineString(new LineString(array(
                    array(-63.676586151123,-31.367415770489),
                    array(-63.673968315125,-31.366206539217),
                    array(-63.67280960083,-31.364704139298),
                    array(-63.67169380188,-31.363788030001),
                    array(-63.670706748962,-31.363641451685),
                    array(-63.669762611389,-31.364154474791),
                    array(-63.668003082275,-31.365070580517),
                    array(-63.666973114014,-31.364814071814),
                    array(-63.666501045227,-31.363788030001),
                    array(-63.664870262146,-31.362248946282),
                    array(-63.662981987,-31.360783128836),
                    array(-63.661994934082,-31.35942722735),
                    array(-63.66156578064,-31.357741484721),
                    array(-63.661437034607,-31.355835826222),
                    array(-63.66014957428,-31.353123861001),
                    array(-63.658862113953,-31.352500830916),
                    array(-63.656415939331,-31.352061042488),
                    array(-63.654913902283,-31.352354235002),
                    array(-63.653645516024,-31.351764794584),
                    array(-63.651242256747,-31.349749064959),
                    array(-63.645467759343,-31.347546983301),
                    array(-63.64392280695,-31.346594055584),
                    array(-63.640060425969,-31.342415720095),
                    array(-63.639030457707,-31.341096207173),
                    array(-63.637914658757,-31.340949593483),
                    array(-63.634138108464,-31.341389433866),
                    array(-63.629417420598,-31.341242820633),
                    array(-63.627786637517,-31.341829272192),
                    array(-63.626585007878,-31.343295385094),
                    array(-63.626070023747,-31.345347904772),
                    array(-63.625984193059,-31.346374147817),
                    array(-63.624610902043,-31.346887265141),
                    array(-63.622636796208,-31.347327077762),
                    array(-63.621606827946,-31.34813339556),
                    array(-63.621349335881,-31.349746010418),
                    array(-63.621349335881,-31.351285298808),
                    array(-63.620491028996,-31.35238477509),
                    array(-63.619375230046,-31.352677966594),
                    array(-63.618345261784,-31.352824562004),
                    array(-63.616971970769,-31.352604668804),
                    array(-63.616285325261,-31.351798389339),
                    array(-63.614997864934,-31.351358597627),
                    array(-63.612852097722,-31.351798389339),
                    array(-63.611049653264,-31.351065402009),
                    array(-63.60898971674,-31.349086307681),
                    array(-63.607530595036,-31.347473681512),
                    array(-63.605556489201,-31.346154239536),
                    array(-63.604955674382,-31.344028432977),
                    array(-63.60504150507,-31.342928859011),
                    array(-63.607530595036,-31.341096207173),
                    array(-63.60959053156,-31.339190211392),
                    array(-63.608732224675,-31.337650725074),
                    array(-63.60787391779,-31.336037902868),
                    array(-63.606586457463,-31.334864923902),
                    array(-63.60452652094,-31.334718300503),
                    array(-63.602552415105,-31.335451415212),
                    array(-63.601608277531,-31.336917627498),
                    array(-63.600063325139,-31.338237199022),
                    array(-63.598260880681,-31.338383816938),
                    array(-63.59602928278,-31.338677052084),
                    array(-63.595342637273,-31.337724034517),
                    array(-63.595771790715,-31.336184524211),
                    array(-63.595771790715,-31.334864923902),
                    array(-63.595085145207,-31.333691930314),
                    array(-63.594226838322,-31.332738862259),
                    array(-63.592767716618,-31.332518922106),
                    array(-63.591480256291,-31.333471992389),
                    array(-63.59096527216,-31.334938235515),
                    array(-63.590793610783,-31.336477766211),
                    array(-63.590192795964,-31.337870653233),
                    array(-63.589162827702,-31.338237199022),
                    array(-63.587446213933,-31.338603743383),
                    array(-63.585729600163,-31.338310508009),
                    array(-63.584098817082,-31.337504106016),
                    array(-63.58255386469,-31.337504106016),
                    array(-63.580493928166,-31.337577415573),
                    array(-63.578691483708,-31.336257834797),
                    array(-63.576998711214,-31.334611387837),
                    array(-63.575305938721,-31.33296491207),
                    array(-63.572559356689,-31.332231777991),
                    array(-63.569641113281,-31.331205380684)
                ), 4326))
        );

        $observationPointId = ObservationPointId::generate();
        $riverBoundary = $riverBoundary->addObservationPoint(ObservationPoint::fromIdNameAndGeometry(
            $observationPointId,
            ObservationPointName::fromString('OP1'),
            Geometry::fromPoint(new Point(-63.67280960083,-31.364704139298, 4326))
        ));

        /** @var RiverBoundary $riverBoundary */
        $riverBoundary = $riverBoundary->addRiverStageToObservationPoint($observationPointId, RiverDateTimeValue::fromParams(
            new \DateTimeImmutable('1.1.2015'),
            446,
            444,
            200
        ));

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $riverBoundary));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $boundaryId);
        $this->assertCount(135, $activeCells->cells());
    }

    public function test_add_chd_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->create_model_with_name_bounding_box_and_grid_size($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $chdBoundary = ConstantHeadBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestChd'),
            Geometry::fromLineString(new LineString(array(
                array(-63.687336, -31.313615),
                array(-63.569260, -31.313615)
            ), 4326))
        );

        $observationPointId = ObservationPointId::generate();
        $chdBoundary = $chdBoundary->addObservationPoint(ObservationPoint::fromIdNameAndGeometry(
            $observationPointId,
            ObservationPointName::fromString('OP1'),
            Geometry::fromPoint(new Point(-63.687336, -31.313615, 4326))
        ));

        $chdBoundary = $chdBoundary->addConstantHeadToObservationPoint($observationPointId, ConstantHeadDateTimeValue::fromParams(
            new \DateTimeImmutable('1.1.2015'),
            450,
            450
        ));

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $chdBoundary));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $boundaryId);
        $this->assertCount(75, $activeCells->cells());
    }

    public function test_add_ghb_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->create_model_with_name_bounding_box_and_grid_size($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $ghbBoundary = GeneralHeadBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestGhb'),
            Geometry::fromLineString(new LineString(array(
                array(-63.687336, -31.313615),
                array(-63.569260, -31.313615)
            ), 4326))
        );

        $observationPointId = ObservationPointId::generate();
        $ghbBoundary = $ghbBoundary->addObservationPoint(ObservationPoint::fromIdNameAndGeometry(
            $observationPointId,
            ObservationPointName::fromString('OP1'),
            Geometry::fromPoint(new Point(-63.687336, -31.313615, 4326))
        ));

        $ghbBoundary = $ghbBoundary->addGeneralHeadValueToObservationPoint($observationPointId, GeneralHeadDateTimeValue::fromParams(
            new \DateTimeImmutable('1.1.2015'),
            450,
            100
        ));

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $ghbBoundary));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $boundaryId);
        $this->assertCount(75, $activeCells->cells());
    }

    public function test_add_rch_boundary_to_model_and_calculate_active_cells(): void
    {
        $ownerId = UserId::generate();
        $modelId = ModflowId::generate();
        $this->create_model_with_name_bounding_box_and_grid_size($ownerId, $modelId);

        $boundaryId = BoundaryId::generate();
        $rchBoundary = RechargeBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestRch'),
            Geometry::fromPolygon(new Polygon(
                array(
                    array(
                        array(-63.65, -31.31),
                        array(-63.65, -31.36),
                        array(-63.58, -31.36),
                        array(-63.58, -31.31),
                        array(-63.65, -31.31)
                    )
                ), 4326
            ))
        );

        $rchBoundary = $rchBoundary->addRecharge(RechargeDateTimeValue::fromParams(
            new \DateTimeImmutable('1.1.2015'),
            3.29e-4
        ));

        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $rchBoundary));
        $activeCells = $this->container->get('inowas.model_boundaries_finder')->findBoundaryActiveCells($modelId, $boundaryId);
        $this->assertCount(1610, $activeCells->cells());
    }

    private function create_model_with_name_bounding_box_and_grid_size(UserId $ownerId, ModflowId $modelId): void
    {
        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId,$modelId, Modelname::fromString('TestModel')));

        $box = $this->container->get('inowas.geotools')->projectBoundingBox(BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615, 4326), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $gridSize = GridSize::fromXY(75, 40);
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modelId, $gridSize));
    }

    //    public function testAddBoundaryToScenario()
//    {
//        $ownerId = UserId::generate();
//        $modflowModelId = ModflowId::generate();
//        $scenarioId = ModflowId::generate();
//        $scenarioWellId = BoundaryId::generate();
//        $well = WellBoundary::create($scenarioWellId);
//
//        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modflowModelId));
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//        $this->commandBus->dispatch(AddBoundary::toScenario($ownerId, $modflowModelId, $scenarioId, $well));
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertCount(1, $model->scenarios());
//        $this->assertCount(0, $model->boundaries());
//
//        /** @var ModflowModelAggregate $scenario */
//        $scenario = array_values($model->scenarios())[0];
//        $this->assertCount(1, $scenario->boundaries());
//        $this->assertEquals($well, $scenario->boundaries()[$well->boundaryId()->toString()]);
//    }
//
//    public function testChangeBaseModelMetadata()
//    {
//        $ownerId = UserId::generate();
//        $modflowModelId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modflowModelId));
//        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modflowModelId, Modelname::fromString('MyNewModel')));
//        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($ownerId, $modflowModelId, ModflowModelDescription::fromString('MyNewModelDescription')));
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertEquals(Modelname::fromString('MyNewModel'), $model->name());
//        $this->assertEquals(ModflowModelDescription::fromString('MyNewModelDescription'), $model->description());
//
//        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modflowModelId, Modelname::fromString('MyNewModelChanged')));
//        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($ownerId, $modflowModelId, ModflowModelDescription::fromString('MyNewModelDescriptionChanged')));
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertEquals(Modelname::fromString('MyNewModelChanged'), $model->name());
//        $this->assertEquals(ModflowModelDescription::fromString('MyNewModelDescriptionChanged'), $model->description());
//    }
//
//    public function testChangeScenarioMetadata()
//    {
//        $ownerId = UserId::generate();
//        $modflowModelId = ModflowId::generate();
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modflowModelId));
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//
//        $this->commandBus->dispatch(ChangeModflowModelName::forScenario($ownerId, $modflowModelId, $scenarioId, SoilmodelName::fromString('MyNewModel')));
//        $this->commandBus->dispatch(ChangeModflowModelDescription::forScenario($ownerId, $modflowModelId, $scenarioId, SoilModelDescription::fromString('MyNewModelDescription')));
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modflowModelId);
//
//        /** @var ModflowModelAggregate $scenario */
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $this->assertEquals(SoilmodelName::fromString('MyNewModel'), $scenario->name());
//        $this->assertEquals(SoilModelDescription::fromString('MyNewModelDescription'), $scenario->description());
//
//        $this->commandBus->dispatch(ChangeModflowModelName::forScenario($ownerId, $modflowModelId, $scenarioId, SoilmodelName::fromString('MyNewModelChanged')));
//        $this->commandBus->dispatch(ChangeModflowModelDescription::forScenario($ownerId, $modflowModelId, $scenarioId, SoilModelDescription::fromString('MyNewModelDescriptionChanged')));
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modflowModelId);
//
//        /** @var ModflowModelAggregate $scenario */
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $this->assertEquals(SoilmodelName::fromString('MyNewModelChanged'), $scenario->name());
//        $this->assertEquals(SoilModelDescription::fromString('MyNewModelDescriptionChanged'), $scenario->description());
//    }
//
//    public function testModflowModelCommands()
//    {
//        $ownerId = UserId::generate();
//        $modflowModelId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modflowModelId));
//        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modflowModelId, SoilmodelName::fromString('MyNewModel')));
//        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($ownerId, $modflowModelId, SoilModelDescription::fromString('MyNewModelDescription')));
//
//        $areaId = BoundaryId::generate();
//        $area = AreaBoundary::create($areaId);
//        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modflowModelId, $area));
//        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modflowModelId, BoundingBox::fromCoordinates(1, 2, 3, 4, 5)));
//        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modflowModelId, GridSize::fromXY(50, 60)));
//
//        $soilmodelId = SoilmodelId::generate();
//        $this->commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($modflowModelId, $soilmodelId));
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertInstanceOf(ModflowModelAggregate::class, $model);
//        $this->assertEquals($ownerId, $model->ownerId());
//        $this->assertEquals($modflowModelId, $model->modflowModelId());
//        $this->assertEquals(SoilmodelName::fromString('MyNewModel'), $model->name());
//        $this->assertEquals(SoilModelDescription::fromString('MyNewModelDescription'), $model->description());
//        $this->assertEquals($areaId, $model->area()->boundaryId());
//        $this->assertEquals(BoundingBox::fromCoordinates(1, 2, 3, 4, 5), $model->boundingBox());
//        $this->assertEquals(GridSize::fromXY(50, 60), $model->gridSize());
//        $this->assertEquals($soilmodelId, $model->soilmodelId());
//
//        $baseModelWellId = BoundaryId::generate();
//        $well = WellBoundary::create($baseModelWellId);
//        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modflowModelId, $well));
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertCount(1, $model->boundaries());
//
//        $this->commandBus->dispatch(RemoveBoundary::fromBaseModel($ownerId, $modflowModelId, $baseModelWellId));
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertCount(0, $model->boundaries());
//
//        $baseModelWellId = BoundaryId::generate();
//        $well = WellBoundary::create($baseModelWellId);
//        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modflowModelId, $well));
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertCount(1, $model->boundaries());
//
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertCount(1, $model->scenarios());
//
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//
//        $model = $this->modelRepository->get($modflowModelId);
//        $this->assertCount(4, $model->scenarios());
//
//        /** @var ModflowModelAggregate $scenario * */
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $this->assertInstanceOf(ModflowModelAggregate::class, $scenario);
//        $this->assertEquals('Scenario of MyNewModel', $scenario->name()->toString());
//        $this->assertCount(1, $scenario->boundaries());
//
//        $scenarioWellId = BoundaryId::generate();
//        $well = WellBoundary::create($scenarioWellId);
//        $this->commandBus->dispatch(AddBoundary::toScenario($ownerId, $modflowModelId, $scenarioId, $well));
//
//        $model = $this->modelRepository->get($modflowModelId);
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $this->assertCount(2, $scenario->boundaries());
//
//        /** @var \Inowas\Common\Boundaries\ModflowBoundary $well */
//        $well = $scenario->boundaries()[$scenarioWellId->toString()];
//        $this->assertInstanceOf(WellBoundary::class, $well);
//        $this->assertEquals($scenarioWellId, $well->boundaryId());
//
//        $this->commandBus->dispatch(RemoveBoundary::fromScenario($ownerId, $modflowModelId, $scenarioId, $well->boundaryId()));
//        $model = $this->modelRepository->get($modflowModelId);
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $this->assertCount(1, $scenario->boundaries());
//
//        $this->commandBus->dispatch(RemoveBoundary::fromScenario($ownerId, $modflowModelId, $scenarioId, $baseModelWellId));
//        $model = $this->modelRepository->get($modflowModelId);
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $this->assertCount(0, $scenario->boundaries());
//
//        $scenarioWellId = BoundaryId::generate();
//        $well = WellBoundary::create($scenarioWellId);
//        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modflowModelId, $well));
//
//        $well->test = 'testBaseModel';
//        $this->commandBus->dispatch(UpdateBoundary::ofBaseModel($ownerId, $modflowModelId, $well));
//        $model = $this->modelRepository->get($modflowModelId);
//        $well = $model->boundaries()[$scenarioWellId->toString()];
//        $this->assertEquals('testBaseModel', $well->test);
//
//        $scenarioWellId = BoundaryId::generate();
//        $well = WellBoundary::create($scenarioWellId);
//        $this->commandBus->dispatch(AddBoundary::toScenario($ownerId, $modflowModelId, $scenarioId, $well));
//
//        $well->test = 'testScenario';
//        $this->commandBus->dispatch(UpdateBoundary::ofScenario($ownerId, $modflowModelId, $scenarioId, $well));
//        $model = $this->modelRepository->get($modflowModelId);
//        $scenario = $model->scenarios()[$scenarioId->toString()];
//        $well = $scenario->boundaries()[$scenarioWellId->toString()];
//        $this->assertEquals('testScenario', $well->test);
//
//        $calculationId = ModflowId::generate();
//        $start = DateTime::fromDateTime(new \DateTime('01.01.2015'));
//        $end = DateTime::fromDateTime(new \DateTime('01.01.2015'));
//        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modflowModelId, $start, $end));
//
//        /** @var ModflowCalculationAggregate $calculation */
//        $calculation = $this->calculationRepository->get($calculationId);
//        $this->assertInstanceOf(ModflowCalculationAggregate::class, $calculation);
//        $this->assertEquals($calculationId, $calculation->calculationId());
//        $this->assertEquals($modflowModelId, $calculation->modelId());
//        $this->assertEquals($ownerId, $calculation->ownerId());
//        $this->assertEquals($soilmodelId, $calculation->soilModelId());
//
//
//        /*
//        $times = [];
//        for ($i = 1; $i < 1096; $i++){
//            if ($i%15==0){
//                $times[] = $i;
//            }
//        }
//
//        foreach ($times as $time){
//            $heads = $this->loadHeads(0, $time, [0, 1, 2, 3]);
//            $calculationResult = CalculationResult::fromParameters(
//                TotalTime::fromInt($time),
//                CalculationResultType::fromString(CalculationResultType::HEAD_TYPE),
//                CalculationResultData::from3dArray($heads)
//            );
//            $this->commandBus->dispatch(AddResultToCalculation::to($calculationId, $calculationResult));
//            unset($calculationResult);
//        }
//        */
//
//        $calculationId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modflowModelId, $scenarioId));
//        $calculation = $this->calculationRepository->get($calculationId);
//        $this->assertInstanceOf(ModflowCalculationAggregate::class, $calculation);
//        $this->assertEquals($calculationId->toString(), $calculation->calculationId()->toString());
//        $this->assertEquals($scenarioId, $calculation->modelId());
//        $this->assertEquals($ownerId, $calculation->ownerId());
//        $this->assertEquals($soilmodelId, $calculation->soilModelId());
//
//        $calculationResult = CalculatedResult::fromParameters(
//            TotalTime::fromInt(1),
//            ResultType::fromString(ResultType::HEAD_TYPE),
//            HeadData::from3dArray([[[1,2,3]]])
//        );
//        $this->commandBus->dispatch(AddCalculatedHead::to($calculationId, $calculationResult));
//
//        $headsS0L3 = $this->loadHeadsFromFile(__DIR__."/data/base_scenario_head_layer_3.json");
//        $calculationId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modflowModelId));
//        $this->commandBus->dispatch(AddCalculatedHead::to($calculationId,
//            CalculatedResult::fromParameters(
//                TotalTime::fromInt(120),
//                ResultType::fromString(ResultType::HEAD_TYPE),
//                HeadData::from3dArray([[], [], $headsS0L3, []])
//            )
//        ));
//
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//        $headsS1L3 = $this->loadHeadsFromFile(__DIR__."/data/scenario_1_head_layer_3.json");
//        $calculationId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modflowModelId, $scenarioId));
//        $this->commandBus->dispatch(AddCalculatedHead::to($calculationId,
//            CalculatedResult::fromParameters(
//                TotalTime::fromInt(120),
//                ResultType::fromString(ResultType::HEAD_TYPE),
//                HeadData::from3dArray([[], [], $headsS1L3, []])
//            )
//        ));
//
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//        $headsS2L3 = $this->loadHeadsFromFile(__DIR__."/data/scenario_2_head_layer_3.json");
//        $calculationId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modflowModelId, $scenarioId));
//        $this->commandBus->dispatch(AddCalculatedHead::to($calculationId,
//            CalculatedResult::fromParameters(
//                TotalTime::fromInt(120),
//                ResultType::fromString(ResultType::HEAD_TYPE),
//                HeadData::from3dArray([[], [], $headsS2L3, []])
//            )
//        ));
//
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modflowModelId, $scenarioId));
//        $headsS3L3 = $this->loadHeadsFromFile(__DIR__."/data/scenario_3_head_layer_3.json");
//        $calculationId = ModflowId::generate();
//        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelAndScenarioId($calculationId, $ownerId, $modflowModelId, $scenarioId));
//        $this->commandBus->dispatch(AddCalculatedHead::to($calculationId,
//            CalculatedResult::fromParameters(
//                TotalTime::fromInt(120),
//                ResultType::fromString(ResultType::HEAD_TYPE),
//                HeadData::from3dArray([[], [], $headsS3L3, []])
//            )
//        ));
//
//        /** @var ModflowCalculationAggregate $calculation */
//        #$calculation = $this->calculationRepository->get($calculationId);
//        #dump($calculation->results());
//        #dump($this->model_calculations_projector->getData());
//    }
//
//    public function testModflowModelCommandsAgain()
//    {
//        $ownerId = UserId::generate();
//        $modelId = ModflowId::generate();
//
//        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modelId));
//        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modelId, SoilmodelName::fromString('BaseModel INOWAS Hanoi')));
//        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel(
//            $ownerId,
//            $modelId,
//            SoilModelDescription::fromString(
//                'Application of managed aquifer recharge for maximization of water storage capacity in Hanoi.'
//            )
//        ));
//
//        $area = AreaBoundary::create(BoundaryId::generate())
//            ->setName(BoundaryName::fromString('Hanoi Area'))
//            ->setGeometry(Geometry::fromPolygon(new Polygon(array(
//            array(
//                array(105.790767733626808, 21.094425932026443),
//                array(105.796959843400032, 21.093521487879368),
//                array(105.802017060333782, 21.092234483652170),
//                array(105.808084259744490, 21.090442258424751),
//                array(105.812499379361824, 21.088745285770433),
//                array(105.817189857772419, 21.086246452411380),
//                array(105.821849880920155, 21.083084791161816),
//                array(105.826206685192972, 21.080549811906632),
//                array(105.829745666549428, 21.077143263497668),
//                array(105.833738284468225, 21.073871989488410),
//                array(105.837054371969458, 21.068790508713093),
//                array(105.843156477826938, 21.061619066459148),
//                array(105.845257297050807, 21.058494488216656),
//                array(105.848091064693264, 21.055416254106909),
//                array(105.850415052797018, 21.051740212147806),
//                array(105.853986426189834, 21.047219935885728),
//                array(105.857317797743207, 21.042700799256870),
//                array(105.860886165285677, 21.037730164508108),
//                array(105.862781077291359, 21.033668431680731),
//                array(105.865628458812012, 21.028476242159179),
//                array(105.867512713611035, 21.022613568026749),
//                array(105.869402048566840, 21.017651320651229),
//                array(105.871388782041976, 21.013426442220442),
//                array(105.872849945737570, 21.008166192541132),
//                array(105.876181664767913, 21.003946864458868),
//                array(105.882508712001197, 21.001813076331899),
//                array(105.889491767034770, 21.000288452359857),
//                array(105.894324807327010, 20.997811850332017),
//                array(105.898130162725238, 20.994990356212355),
//                array(105.903035574892471, 20.989098851962478),
//                array(105.905619253163707, 20.984707849769400),
//                array(105.905107309855680, 20.977094091795209),
//                array(105.901707144804220, 20.969670720258843),
//                array(105.896052272867848, 20.959195015805960),
//                array(105.886865167028475, 20.950138230157627),
//                array(105.877901274443431, 20.947208019282808),
//                array(105.834499067698161, 20.951978316227517),
//                array(105.806257646336405, 20.968923300374374),
//                array(105.781856978173835, 21.008608549010258),
//                array(105.768216532593982, 21.039487418417067),
//                array(105.774357585691064, 21.072902571997240),
//                array(105.777062025914603, 21.090749775344797),
//                array(105.783049106327312, 21.093961473086512),
//                array(105.790767733626808, 21.094425932026443)
//            )
//        ), 4326)));
//
//        $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $area));
//
//        $box = $this->geoTools->transformBoundingBox(new BoundingBox(578205, 594692, 2316000, 2333500, 32648), 4326);
//        $boundingBox = BoundingBox::fromEPSG4326Coordinates(
//            $box->getXMin(),
//            $box->getXMax(),
//            $box->getYMin(),
//            $box->getYMax()
//        );
//
//        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));
//        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modelId, GridSize::fromXY(165, 175)));
//
//        $wells = [[23, 'LN11', 11788984.59457647800445557, 2389010.63655604887753725, -40, -70, 4320, -2135, 11788984.59, 2389010.64]];
//
//        $header = array('id', 'name', 'wkt_x', 'wkt_y', 'ztop', 'zbot', 'stoptime', 'pumpingrate', 'x', 'y');
//        foreach ($wells as $row) {
//            $wellData = array_combine($header, $row);
//            $well = WellBoundary::createWithAllParams(
//                BoundaryId::generate(),
//                BoundaryName::fromString($wellData['name']),
//                Geometry::fromPoint($this->geoTools->projectPoint(new Point($wellData['x'], $wellData['y'], 3857), Srid::fromInt(4326))),
//                WellType::fromString(WellType::TYPE_PUBLIC_WELL),
//                LayerNumber::fromInteger(4),
//                WellDateTimeValue::fromValue($wellData['pumpingrate'])
//            );
//
//            $this->commandBus->dispatch(AddBoundary::toBaseModel($ownerId, $modelId, $well));
//        }
//
//        /** @var ModflowModelAggregate $model */
//        $model = $this->modelRepository->get($modelId);
//        $this->assertInstanceOf(AreaBoundary::class, $model->area());
//        $this->assertInstanceOf(BoundaryId::class, $model->area()->boundaryId());
//        $this->assertEquals($area->boundaryId(), $model->area()->boundaryId());
//        $this->assertInstanceOf(BoundaryName::class, $model->area()->name());
//        $this->assertEquals('Hanoi Area', $model->area()->name()->toString());
//        $this->assertInstanceOf(Geometry::class, $model->area()->geometry());
//        $this->assertInstanceOf(BoundingBox::class, $model->boundingBox());
//        $this->assertEquals($boundingBox, $model->boundingBox());
//        $this->assertInstanceOf(GridSize::class, $model->gridSize());
//        $this->assertEquals(GridSize::fromXY(165, 175), $model->gridSize());
//        $this->assertCount(1, $model->boundaries());
//
//        /** @var WellBoundary $well */
//        $well = array_values($model->boundaries())[0];
//        $this->assertInstanceOf(BoundaryId::class, $well->boundaryId());
//        $this->assertEquals('LN11', $well->name()->toString());
//        $this->assertEquals('{"type":"Point","coordinates":[105.90225041447,20.975946029725]}', $well->geometry()->toJson());
//        $this->assertEquals('puw', $well->wellType()->type());
//        $this->assertEquals(4, $well->layerNumber()->toInteger());
//        $this->assertEquals(-2135, $well->pumpingRate()->toFloat());
//
//        $scenarioId = ModflowId::generate();
//        $this->commandBus->dispatch(AddModflowScenario::from($ownerId, $modelId, $scenarioId));
//        $model = $this->modelRepository->get($modelId);
//        $this->assertCount(1, $model->scenarios());
//
//        /** @var ModflowModelAggregate $scenario */
//        $scenario = array_values($model->scenarios())[0];
//        $this->assertEquals($scenarioId, $scenario->modflowModelId());
//        $this->assertEquals($ownerId, $scenario->ownerId());
//
//        $well = array_values($scenario->boundaries())[0];
//        $this->assertInstanceOf(BoundaryId::class, $well->boundaryId());
//        $this->assertEquals('LN11', $well->name()->toString());
//        $this->assertEquals('{"type":"Point","coordinates":[105.90225041447,20.975946029725]}', $well->geometry()->toJson());
//        $this->assertEquals('puw', $well->wellType()->type());
//        $this->assertEquals(4, $well->layerNumber()->toInteger());
//        $this->assertEquals(-2135, $well->pumpingRate()->toFloat());
//    }
//
//    private function loadHeads($scenarioNumber, $time, $layers)
//    {
//        $heads = [];
//        foreach ($layers as $layer){
//            $filename = sprintf(__DIR__.'/../../../Modflow/DataFixtures/ES/Scenarios/Hanoi/heads/heads_S%s-T%s-L%s.json', $scenarioNumber, $time, $layer);
//            $heads[$layer] = $this->loadHeadsFromFile($filename);
//            echo $filename."\r\n";
//        }
//
//        return $heads;
//    }
//
//    private function loadHeadsFromFile($filename){
//
//        if (!file_exists($filename) || !is_readable($filename)) {
//            echo "File not found.\r\n";
//            return FALSE;
//        }
//
//        $headsJSON = file_get_contents($filename, true);
//        $heads = json_decode($headsJSON, true);
//
//        for ($iy = 0; $iy < count($heads); $iy++){
//            for ($ix = 0; $ix < count($heads[0]); $ix++){
//                if ($heads[$iy][$ix] <= -9999){
//                    $heads[$iy][$ix] = null;
//                }
//            }
//        }
//
//        unset($headsJSON);
//        return $heads;
//    }
}
