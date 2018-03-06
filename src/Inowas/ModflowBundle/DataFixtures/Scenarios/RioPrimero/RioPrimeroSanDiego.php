<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero;

use Inowas\Common\Boundaries\Metadata;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RechargeDateTimeValue;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\AffectedCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Interpolation\PointValue;
use Inowas\Common\Modflow\Botm;
use Inowas\Common\Modflow\Hani;
use Inowas\Common\Modflow\Hk;
use Inowas\Common\Modflow\Layavg;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\Laywet;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Modflow\Ss;
use Inowas\Common\Modflow\Sy;
use Inowas\Common\Modflow\Top;
use Inowas\Common\Modflow\Vka;
use Inowas\Common\Soilmodel\Layer;
use Inowas\Common\Soilmodel\LayerId;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\AddLayer;
use Inowas\ModflowModel\Model\Command\ChangeDescription;
use Inowas\ModflowModel\Model\Command\ChangeName;
use Inowas\ModflowModel\Model\Packages\OcStressPeriod;
use Inowas\ModflowModel\Model\Packages\OcStressPeriodData;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\ParameterName;
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Command\ChangeFlowPackage;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowBundle\DataFixtures\Scenarios\LoadScenarioBase;
use Inowas\ModflowModel\Model\Command\UpdateModflowPackageParameter;
use Inowas\ModflowModel\Model\Command\UpdateStressPeriods;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenario;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
use Inowas\Soilmodel\Model\LayerInterpolationConfiguration;

class RioPrimeroSanDiego extends LoadScenarioBase
{

    /**
     *
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     */
    public function load(): void
    {
        $this->loadUsers($this->container->get('fos_user.user_manager'));

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = UserId::fromString($this->ownerId);

        $baseModelId = ModflowId::generate();
        $polygon = new Polygon([[
            [-63.584285, -31.303127],
            [-63.61835, -31.302526],
            [-63.667798, -31.309436],
            [-63.690513, -31.328278],
            [-63.694843, -31.364834],
            [-63.666198, -31.378454],
            [-63.626116, -31.376053],
            [-63.565588, -31.362174],
            [-63.554357, -31.338634],
            [-63.56926, -31.313615],
            [-63.584285, -31.303127]
        ]], 4326);
        $boundingBox = $this->container->get('inowas.geotools.geotools_service')->getBoundingBox(Geometry::fromPolygon($polygon));
        $gridSize = GridSize::fromXY(75, 40);

        $commandBus->dispatch(CreateModflowModel::newWithAllParams(
            $ownerId,
            $baseModelId,
            Name::fromString('BaseModel Rio Primero 2015'),
            Description::fromString('BaseModel Rio Primero 2015'),
            $polygon,
            $gridSize,
            $boundingBox,
            TimeUnit::fromInt(TimeUnit::DAYS),
            LengthUnit::fromInt(LengthUnit::METERS),
            Visibility::public ()
        ));

        /* Setup layer */
        $boreHoles = array(
            array(new Point(-63.64698, -31.32741, 4326), 'GP1', 465, 395),
            array(new Point(-63.64630, -31.34237, 4326), 'GP2', 460, 390),
            array(new Point(-63.64544, -31.35967, 4326), 'GP3', 467, 397),
            array(new Point(-63.61591, -31.32404, 4326), 'GP4', 463, 393),
            array(new Point(-63.61420, -31.34383, 4326), 'GP5', 463, 393),
            array(new Point(-63.61506, -31.36011, 4326), 'GP6', 465, 395),
            array(new Point(-63.58536, -31.32653, 4326), 'GP7', 465, 395),
            array(new Point(-63.58261, -31.34266, 4326), 'GP8', 460, 390),
            array(new Point(-63.58459, -31.35573, 4326), 'GP9', 460, 390)
        );

        /* Interpolation */
        $interpolation = new LayerInterpolationConfiguration();
        $interpolation->addMethod(LayerInterpolationConfiguration::METHOD_GAUSSIAN);
        $interpolation->addMethod(LayerInterpolationConfiguration::METHOD_MEAN);
        $interpolation->setBoundingBox($boundingBox);
        $interpolation->setGridSize($gridSize);

        foreach ($boreHoles as $boreHole) {
            $interpolation->addPointValue(new PointValue($boreHole[0], $boreHole[2]));
        }

        $result = $this->container->get('inowas.soilmodel.interpolation_service')->interpolate($interpolation);
        $top = Top::fromValue($result->result());


        $interpolation = $interpolation->clear();
        foreach ($boreHoles as $boreHole) {
            $interpolation->addPointValue(new PointValue($boreHole[0], $boreHole[3]));
        }

        $result = $this->container->get('inowas.soilmodel.interpolation_service')->interpolate($interpolation);
        $bottom = Botm::fromValue($result->result());

        $name = Name::fromString('Surface Layer');
        $description = Description::fromString('the one and only.');
        $layerId = LayerId::fromString($name->slugified());
        $number = LayerNumber::fromInt(0);

        $layer = Layer::fromParams(
            $layerId,
            $name,
            $description,
            $number,
            $top,
            $bottom,
            Hk::fromValue(10),
            Hani::fromValue(1),
            Vka::fromValue(1),
            Layavg::fromInt(Layavg::TYPE_HARMONIC_MEAN),
            Laytyp::fromValue(Laytyp::TYPE_CONVERTIBLE),
            Laywet::fromFloat(Laywet::WETTING_INACTIVE),
            Ss::fromFloat(1e-5),
            Sy::fromFloat(0.2)
        );

        $commandBus->dispatch(AddLayer::forModflowModel($ownerId, $baseModelId, $layer));


        /* Add GeneralHeadBoundary on the east bound of the model */
        $ghbEast = GeneralHeadBoundary::createWithParams(
            Name::fromString('General Head East'),
            Geometry::fromLineString(
                new LineString([
                        [-63.616109, -31.30281],
                        [-63.584665, -31.303499],
                        [-63.569407, -31.313727],
                        [-63.554574, -31.338599],
                        [-63.565714, -31.361927],
                        [-63.625478, -31.375684]]
                )
            ),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()
        );

        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            ObservationPointId::fromString('op1'),
            BoundaryType::fromString(BoundaryType::GENERAL_HEAD),
            Name::fromString('OP 1'),
            new Point([-63.584664574591, -31.303499350272])
        );

        /** @var GeneralHeadBoundary $ghbEast */
        $ghbEast->addObservationPoint($observationPoint);
        $ghbEast->addGeneralHeadValueToObservationPoint(
            ObservationPointId::fromString('op1'),
            GeneralHeadDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                442,
                200
            )
        );

        $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $ghbEast));

        /* Add GeneralHeadBoundary on the west bound of the model */
        /** @var GeneralHeadBoundary $ghbWest */
        $ghbWest = GeneralHeadBoundary::createWithParams(
            Name::fromString('General Head West'),
            Geometry::fromLineString(new LineString([
                [-63.626285, -31.375908],
                [-63.666093, -31.378371],
                [-63.694553, -31.364649],
                [-63.689123, -31.328756],
                [-63.666594, -31.310053],
                [-63.617433, -31.302798]
            ])),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()
        );

        // OP 1
        $observationPointId = ObservationPointId::fromString('op1');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::GENERAL_HEAD),
            Name::fromString('OP 1'),
            new Point([-63.617433, -31.302798])
        );

        $ghbWest->addObservationPoint($observationPoint);
        $ghbWest->addGeneralHeadValueToObservationPoint(
            $observationPointId,
            GeneralHeadDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                440,
                200
            )
        );

        // OP 2
        $observationPointId = ObservationPointId::fromString('op2');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::GENERAL_HEAD),
            Name::fromString('OP 2'),
            new Point([-63.694553, -31.364649])
        );

        $ghbWest->addObservationPoint($observationPoint);
        $ghbWest->addGeneralHeadValueToObservationPoint(
            $observationPointId,
            GeneralHeadDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                445,
                200
            )
        );

        // OP 3
        $observationPointId = ObservationPointId::fromString('op3');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::GENERAL_HEAD),
            Name::fromString('OP 3'),
            new Point([-63.666093, -31.378371])
        );

        $ghbWest->addObservationPoint($observationPoint);
        $ghbWest->addGeneralHeadValueToObservationPoint(
            $observationPointId,
            GeneralHeadDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                445,
                200
            )
        );

        // OP 4
        $observationPointId = ObservationPointId::fromString('op4');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::GENERAL_HEAD),
            Name::fromString('OP 4'),
            new Point([-63.626285, -31.375908])
        );

        $ghbWest->addObservationPoint($observationPoint);
        $ghbWest->addGeneralHeadValueToObservationPoint(
            $observationPointId,
            GeneralHeadDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                440,
                200
            )
        );

        $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $ghbWest));

        /*
         * Add RiverBoundary
         * RIV
         */
        $riv = RiverBoundary::createWithParams(
            Name::fromString('Rio Primero River'),
            Geometry::fromLineString(new LineString([
                [-63.682061, -31.370479],
                [-63.679897, -31.368586],
                [-63.678326, -31.368059],
                [-63.676755, -31.367532],
                [-63.673968, -31.366207],
                [-63.67281, -31.364704],
                [-63.671694, -31.363788],
                [-63.670707, -31.363641],
                [-63.669763, -31.364154],
                [-63.668003, -31.365071],
                [-63.666973, -31.364814],
                [-63.666501, -31.363788],
                [-63.66487, -31.362249],
                [-63.662982, -31.360783],
                [-63.661995, -31.359427],
                [-63.661566, -31.357741],
                [-63.661437, -31.355836],
                [-63.66015, -31.353124],
                [-63.658862, -31.352501],
                [-63.656416, -31.352061],
                [-63.654914, -31.352354],
                [-63.653646, -31.351765],
                [-63.651242, -31.349749],
                [-63.645468, -31.347547],
                [-63.643923, -31.346594],
                [-63.64006, -31.342416],
                [-63.63903, -31.341096],
                [-63.637915, -31.34095],
                [-63.634138, -31.341389],
                [-63.629417, -31.341243],
                [-63.627787, -31.341829],
                [-63.626585, -31.343295],
                [-63.62607, -31.345348],
                [-63.625984, -31.346374],
                [-63.624611, -31.346887],
                [-63.622637, -31.347327],
                [-63.621607, -31.348133],
                [-63.621349, -31.349746],
                [-63.621349, -31.351285],
                [-63.620491, -31.352385],
                [-63.619375, -31.352678],
                [-63.618345, -31.352825],
                [-63.616972, -31.352605],
                [-63.616285, -31.351798],
                [-63.614998, -31.351359],
                [-63.612852, -31.351798],
                [-63.61105, -31.351065],
                [-63.60899, -31.349086],
                [-63.607531, -31.347474],
                [-63.605556, -31.346154],
                [-63.604956, -31.344028],
                [-63.605042, -31.342929],
                [-63.607531, -31.341096],
                [-63.609591, -31.33919],
                [-63.608732, -31.337651],
                [-63.607874, -31.336038],
                [-63.606586, -31.334865],
                [-63.604527, -31.334718],
                [-63.602552, -31.335451],
                [-63.601608, -31.336918],
                [-63.600063, -31.338237],
                [-63.598261, -31.338384],
                [-63.596029, -31.338677],
                [-63.595343, -31.337724],
                [-63.595772, -31.336185],
                [-63.595772, -31.334865],
                [-63.595085, -31.333692],
                [-63.594227, -31.332739],
                [-63.592768, -31.332519],
                [-63.59148, -31.333472],
                [-63.590965, -31.334938],
                [-63.590794, -31.336478],
                [-63.590193, -31.337871],
                [-63.589163, -31.338237],
                [-63.587446, -31.338604],
                [-63.58573, -31.338311],
                [-63.584099, -31.337504],
                [-63.582554, -31.337504],
                [-63.580494, -31.337577],
                [-63.578691, -31.336258],
                [-63.576999, -31.334611],
                [-63.574981, -31.332448],
                [-63.57233, -31.331568],
                [-63.570423, -31.331185],
                [-63.569287, -31.330168],
                [-63.568779, -31.328947],
                [-63.56733, -31.327725],
                [-63.565432, -31.326297],
                [-63.563927, -31.326444],
                [-63.563273, -31.325649],
                [-63.563057, -31.324258]
            ], 4326)),
            AffectedCells::create(),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()
        );

        $observationPointId = ObservationPointId::fromString('op1');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::RIVER),
            Name::fromString('OP 1'),
            new Point(-63.679896811664, -31.368585778275)
        );

        /** @var RiverBoundary $riv */
        $riv->addObservationPoint($observationPoint);
        $riv->addRiverStageToObservationPoint(
            $observationPointId,
            RiverDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                445,
                443,
                200
            )
        );

        $observationPointId = ObservationPointId::fromString('op2');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::RIVER),
            Name::fromString('OP 2'),
            new Point(-63.618345, -31.352825)
        );

        /** @var RiverBoundary $riv */
        $riv->addObservationPoint($observationPoint);
        $riv->addRiverStageToObservationPoint(
            $observationPointId,
            RiverDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                444,
                442,
                250
            )
        );

        $observationPointId = ObservationPointId::fromString('op3');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::RIVER),
            Name::fromString('OP 3'),
            new Point(-63.570423, -31.331185)
        );

        /** @var RiverBoundary $riv */
        $riv->addObservationPoint($observationPoint);
        $riv->addRiverStageToObservationPoint(
            $observationPointId,
            RiverDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                443,
                441,
                250
            )
        );

        $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $riv));

        /*
         * Add RechargeBoundary
         */
        /** @var RechargeBoundary $rch */
        $rch = RechargeBoundary::createWithParams(
            Name::fromString('Recharge Boundary'),
            Geometry::fromPolygon(new Polygon(
                [[
                    [-63.712704, -31.299333],
                    [-63.545633, -31.297282],
                    [-63.543739, -31.390413],
                    [-63.713966, -31.389321],
                    [-63.712704, -31.299333]
                ]]
            )),
            AffectedCells::create(),
            AffectedLayers::fromArray([0]),
            Metadata::create()
        );

        $rch->addRecharge(
            RechargeDateTimeValue::fromParams(
                DateTime::fromString('2015-01-01'),
                0.0002
            )
        );

        $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $rch));

        /*
         * Add Wells for the BaseScenario
         */
        $wells = [
            ['name', 'point', 'type', 'layer', 'date', 'pumpingRate'],
            ['Irrigation Well 1', new Point(-63.660372, -31.344513), WellType::TYPE_IRRIGATION_WELL, 0, '2015-01-01', -5000],
            ['Irrigation Well 2', new Point(-63.659952, -31.330144), WellType::TYPE_IRRIGATION_WELL, 0, '2015-01-01', -5000],
            ['Irrigation Well 3', new Point(-63.674691, -31.342506), WellType::TYPE_IRRIGATION_WELL, 0, '2015-01-01', -5000],
            ['Irrigation Well 4', new Point(-63.637379, -31.359613), WellType::TYPE_IRRIGATION_WELL, 0, '2015-01-01', -5000],
            ['Irrigation Well 5', new Point(-63.582069, -31.324063), WellType::TYPE_IRRIGATION_WELL, 0, '2015-01-01', -5000],
            ['Public Well 1', new Point(-63.625402, -31.329897), WellType::TYPE_PUBLIC_WELL, 0, '2015-01-01', -5000],
            ['Public Well 2', new Point(-63.623027, -31.331184), WellType::TYPE_PUBLIC_WELL, 0, '2015-01-01', -5000],
        ];

        $header = null;
        foreach ($wells as $data) {
            if (null === $header) {
                $header = $data;
                continue;
            }

            $data = array_combine($header, $data);

            /** @var WellBoundary $wellBoundary */
            $wellBoundary = WellBoundary::createWithParams(
                Name::fromString($data['name']),
                Geometry::fromPoint($data['point']),
                AffectedCells::create(),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInt($data['layer'])),
                Metadata::create()->addWellType(WellType::fromString($data['type']))
            );

            echo sprintf("Add well with name %s.\r\n", $data['name']);
            $wellBoundary = $wellBoundary->addPumpingRate(
                WellDateTimeValue::fromParams(DateTime::fromString($data['date']), $data['pumpingRate'])
            );
            $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $wellBoundary));
        }

        /* Create calculation and calculate */
        $start = DateTime::fromString('2015-01-01');
        $end = DateTime::fromString('2025-12-31');
        $stressperiods = StressPeriods::create($start, $end, TimeUnit::fromInt(TimeUnit::DAYS));
        $stressperiods->addStressPeriod(StressPeriod::create(0, 365, 1, 1, true));

        $commandBus->dispatch(UpdateStressPeriods::of($ownerId, $baseModelId, $stressperiods));
        $commandBus->dispatch(ChangeFlowPackage::forModflowModel($ownerId, $baseModelId, PackageName::fromString('upw')));
        $ocStressPeriodData = OcStressPeriodData::create()->addStressPeriod(OcStressPeriod::fromParams(0, 0, ['save head', 'save drawdown']));

        $commandBus->dispatch(UpdateModflowPackageParameter::byUserModelIdAndPackageData($ownerId, $baseModelId, PackageName::fromString('oc'), ParameterName::fromString('ocStressPeriodData'), $ocStressPeriodData));

        echo sprintf("Calculate ModflowModel with id %s.\r\n", $baseModelId->toString());
        $commandBus->dispatch(CalculateModflowModel::forModflowModelWitUserId($ownerId, $baseModelId));

        /*
         * Create ScenarioAnalysis from BaseModel
         */
        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $commandBus->dispatch(CreateScenarioAnalysis::byUserWithBaseModelNameAndDescription(
            $scenarioAnalysisId,
            $ownerId,
            $baseModelId,
            ScenarioAnalysisName::fromString('ScenarioAnalysis: Rio Primero 2020'),
            ScenarioAnalysisDescription::fromString('ScenarioAnalysis: Rio Primero 2020'),
            Visibility::public ()
        ));

        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(CreateScenario::byUserWithIds(
            $scenarioAnalysisId,
            $ownerId,
            $baseModelId,
            $scenarioId
        ));

        $commandBus->dispatch(ChangeName::forModflowModel($ownerId, $scenarioId, Name::fromString('Scenario 0: Rio Primero 2020')));
        $commandBus->dispatch(ChangeDescription::forModflowModel($ownerId, $scenarioId, Description::fromString('Future Prediction for the year 2020')));

        $wells = array(
            array('name', 'point', 'type', 'layer', 'date', 'pumpingRate'),
            array('Irrigation Well 6', new Point(-63.65101, -31.33516, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 7', new Point(-63.64792, -31.33546, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 8', new Point(-63.66714, -31.34513, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 9', new Point(-63.6644, -31.34513, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 10', new Point(-63.60363, -31.32578, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 11', new Point(-63.59367, -31.35803, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 12', new Point(-63.60123, -31.32578, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Irrigation Well 13', new Point(-63.58852, -31.35803, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, '2015-01-01', -5000),
            array('Public Well 3', new Point(-63.62383, -31.34, 4326), WellType::TYPE_PUBLIC_WELL, 0, '2015-01-01', -5000),
            array('Public Well 4', new Point(-63.6216, -31.34162, 4326), WellType::TYPE_PUBLIC_WELL, 0, '2015-01-01', -5000),
        );

        $header = null;
        foreach ($wells as $data) {
            if (null === $header) {
                $header = $data;
                continue;
            }

            $data = array_combine($header, $data);
            $wellBoundary = WellBoundary::createWithParams(
                Name::fromString($data['name']),
                Geometry::fromPoint($data['point']),
                AffectedCells::create(),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInt($data['layer'])),
                Metadata::create()->addWellType(WellType::fromString($data['type']))
            );

            echo sprintf("Add well with name %s.\r\n", $data['name']);
            $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(DateTime::fromString($data['date']), $data['pumpingRate']));
            $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $scenarioId, $wellBoundary));
        }

        echo sprintf("Calculate Scenario 0 with id %s.\r\n", $scenarioId->toString());
        $commandBus->dispatch(CalculateModflowModel::forModflowModelWitUserId($ownerId, $scenarioId));
    }
}
