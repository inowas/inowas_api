<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Doctrine\DBAL\Connection;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
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
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Soilmodel\HydraulicAnisotropy;
use Inowas\Common\Soilmodel\HydraulicConductivityX;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\VerticalHydraulicConductivity;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelDescription;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelGridSize;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelName;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowBundle\Command\ModflowEventStoreTruncateCommand;
use Inowas\ModflowBundle\Command\ModflowProjectionsResetCommand;
use Inowas\Soilmodel\Model\Command\AddGeologicalLayerToSoilmodel;
use Inowas\Soilmodel\Model\Command\ChangeSoilmodelDescription;
use Inowas\Soilmodel\Model\Command\ChangeSoilmodelName;
use Inowas\Soilmodel\Model\Command\CreateSoilmodel;
use Inowas\Soilmodel\Model\Command\UpdateGeologicalLayerProperty;
use Inowas\Common\Soilmodel\GeologicalLayer;
use Inowas\Common\Soilmodel\GeologicalLayerDescription;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\GeologicalLayerName;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;
use Inowas\Common\Soilmodel\SoilmodelDescription;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\Common\Soilmodel\SoilmodelName;
use Prooph\EventStore\EventStore;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\EventBus;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class EventSourcingBaseTest extends WebTestCase
{

    /** @var  ContainerInterface */
    protected $container;

    /** @var  CommandBus */
    protected $commandBus;

    /** @var  EventBus */
    protected $eventBus;

    /** @var  EventStore */
    protected $eventStore;

    /** @var  Connection */
    protected $connection;

    public function setUp(): void
    {
        self::bootKernel();

        $application = new Application(static::$kernel);
        $application->add(new ModflowEventStoreTruncateCommand());
        $application->add(new ModflowProjectionsResetCommand());

        $command = $application->find('inowas:es:truncate');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $command = $application->find('inowas:projections:reset');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->container = static::$kernel->getContainer();
        $this->connection = static::$kernel->getContainer()->get('doctrine.dbal.default_connection');
        $this->commandBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->eventBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_event_bus');
        $this->eventStore = static::$kernel->getContainer()->get('prooph_event_store.modflow_model_store');
    }

    /* HELPERS */
    protected function createModelWithName(UserId $ownerId, ModflowId $modelId): void
    {
        $gridSize = GridSize::fromXY(75, 40);
        $this->commandBus->dispatch(CreateModflowModel::newWithId($ownerId, $modelId, $this->createArea(), $gridSize));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId,$modelId, Modelname::fromString('TestModel')));
    }

    protected function createSoilmodel(UserId $ownerId, SoilmodelId $soilmodelId): void
    {
        $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId($ownerId, $soilmodelId));
        $this->commandBus->dispatch(ChangeSoilmodelName::forSoilmodel($ownerId, $soilmodelId, SoilmodelName::fromString('testSoilmodel')));
        $this->commandBus->dispatch(ChangeSoilmodelDescription::forSoilmodel($ownerId, $soilmodelId, SoilmodelDescription::fromString('testSoilmodelDescription')));
    }

    protected function createModelWithSoilmodel(UserId $ownerId, ModflowId $modelId): void
    {

        $area = Area::create(
            BoundaryId::generate(),
            BoundaryName::fromString('Rio Primero Area'),
            new Polygon(array(array(
                array(-63.687336, -31.313615),
                array(-63.687336, -31.367449),
                array(-63.569260, -31.367449),
                array(-63.569260, -31.313615),
                array(-63.687336, -31.313615)
            )), 4326)
        );

        $gridSize = GridSize::fromXY(75, 40);


        $this->commandBus->dispatch(CreateModflowModel::newWithId($ownerId, $modelId, $area, $gridSize));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modelId, Modelname::fromString('Rio Primero Base Model')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel(
            $ownerId,
            $modelId,
            ModflowModelDescription::fromString('Base Model for the scenario analysis 2020 Rio Primero.'))
        );

        $box = $this->container->get('inowas.geotools.geotools_service')->projectBoundingBox(BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615, 4326), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $gridSize = GridSize::fromXY(75, 40);
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modelId, $gridSize));

        /** @var SoilmodelId $soilModelId */
        $soilModelId = SoilmodelId::generate();
        $this->commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($ownerId, $modelId, $soilModelId));
        $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId($ownerId, $soilModelId));
        $this->commandBus->dispatch(ChangeSoilmodelName::forSoilmodel($ownerId, $soilModelId, SoilmodelName::fromString('SoilModel Río Primero')));
        $this->commandBus->dispatch(ChangeSoilmodelDescription::forSoilmodel($ownerId, $soilModelId, SoilmodelDescription::fromString('SoilModel for Río Primero Area')));

        /** @var \Inowas\Common\Soilmodel\GeologicalLayerId $layerId */
        $layerId = GeologicalLayerId::generate();
        $this->commandBus->dispatch(AddGeologicalLayerToSoilmodel::forSoilmodel(
            $ownerId,
            $soilModelId,
            GeologicalLayer::fromParams(
                $layerId,
                Laytyp::fromValue(Laytyp::TYPE_CONVERTIBLE),
                GeologicalLayerNumber::fromInteger(0),
                GeologicalLayerName::fromString('Surface Layer'),
                GeologicalLayerDescription::fromString('the one and only')
            )
        ));

        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, TopElevation::fromLayerValue(430)));
        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, BottomElevation::fromLayerValue(360)));
        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, HydraulicConductivityX::fromLayerValue(10)));
        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, HydraulicAnisotropy::fromLayerValue(1)));
        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, VerticalHydraulicConductivity::fromLayerValue(1)));
        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, SpecificStorage::fromLayerValue(1e-5)));
        $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, SpecificYield::fromLayerValue(0.15)));

    }

    protected function createArea(): Area
    {

        $area = Area::create(
            BoundaryId::generate(),
            BoundaryName::fromString('Rio Primero Area'),
            new Polygon(array(array(
                array(-63.65, -31.31),
                array(-63.65, -31.36),
                array(-63.58, -31.36),
                array(-63.58, -31.31),
                array(-63.65, -31.31)
            )), 4326)
        );

        return $area;
    }

    protected function createConstantHeadBoundaryWithObservationPoint(): ConstantHeadBoundary
    {
        $boundaryId = BoundaryId::generate();

        /** @var ConstantHeadBoundary $chdBoundary */
        $chdBoundary = ConstantHeadBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestChd'),
            Geometry::fromLineString(new LineString(array(
                array(-63.687336, -31.313615),
                array(-63.569260, -31.313615)
            ), 4326)),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
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

        return $chdBoundary;
    }

    protected function createGeneralHeadBoundaryWithObservationPoint(): GeneralHeadBoundary
    {

        $boundaryId = BoundaryId::generate();

        /** @var GeneralHeadBoundary $ghbBoundary */
        $ghbBoundary = GeneralHeadBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestGhb'),
            Geometry::fromLineString(new LineString(array(
                array(-63.687336, -31.313615),
                array(-63.569260, -31.313615)
            ), 4326)),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
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

        return $ghbBoundary;
    }

    protected function createRechargeBoundary(): RechargeBoundary
    {
        $boundaryId = BoundaryId::generate();
        $rchBoundary = RechargeBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('TestRch'),
            Geometry::fromPolygon(new Polygon(
                array(
                    array(
                        array(-63.64, -31.32),
                        array(-63.64, -31.35),
                        array(-63.59, -31.35),
                        array(-63.59, -31.32),
                        array(-63.64, -31.32)
                    )
                ), 4326
            ))
        );

        $rchBoundary = $rchBoundary->addRecharge(RechargeDateTimeValue::fromParams(
            new \DateTimeImmutable('1.1.2015'),
            3.29e-4
        ));

        return $rchBoundary;
    }

    protected function createRiverBoundaryWithObservationPoint(): RiverBoundary
    {
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

        return $riverBoundary;
    }

    protected function createWellBoundary(): WellBoundary
    {
        $boundaryId = BoundaryId::generate();
        $wellBoundary = WellBoundary::createWithParams(
            $boundaryId,
            BoundaryName::fromString('Test Well 1'),
            Geometry::fromPoint(new Point(-63.60, -31.32, 4326)),
            WellType::fromString(WellType::TYPE_INDUSTRIAL_WELL),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(new \DateTimeImmutable('2015-01-01'), -5000));

        return $wellBoundary;
    }
}
