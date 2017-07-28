<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\SanFelipe;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\Metadata;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ObservationPoint;
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
use Inowas\Common\Modflow\StressPeriod;
use Inowas\Common\Modflow\Sy;
use Inowas\Common\Modflow\Top;
use Inowas\Common\Modflow\Vka;
use Inowas\Common\Soilmodel\Layer;
use Inowas\Common\Soilmodel\LayerId;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\AddLayer;
use Inowas\ModflowModel\Model\Packages\OcStressPeriod;
use Inowas\ModflowModel\Model\Packages\OcStressPeriodData;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\ParameterName;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
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

class SanFelipe extends LoadScenarioBase
{

    public function load(): void
    {
        $this->loadUsers($this->container->get('fos_user.user_manager'));
        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = UserId::fromString($this->ownerId);

        $baseModelId = ModflowId::generate();
        $polygon = new Polygon([[
            [ -70.761095655046319, -32.742378587534745 ],
            [ -70.739906154478405, -32.713318701041608 ],
            [ -70.653331909300945, -32.738140687421158 ],
            [ -70.550411477971096, -32.738746101723095 ],
            [ -70.546173577857516, -32.850142333280118 ],
            [ -70.601266279334084, -32.922186635211013 ],
            [ -70.709030025079457, -32.864672276526683 ],
            [ -70.721138311118253, -32.790206317388026 ],
            [ -70.761095655046319, -32.742378587534745 ]
        ]], 4326);

        $boundingBox = $this->container->get('inowas.geotools.geotools_service')->getBoundingBox(Geometry::fromPolygon($polygon));
        $gridSize = GridSize::fromXY(20, 20);

        $commandBus->dispatch(CreateModflowModel::newWithAllParams(
            $ownerId,
            $baseModelId,
            Name::fromString('San Felipe Hydraulic Model'),
            Description::fromString('San Felipe Hydraulic Model'),
            $polygon,
            $gridSize,
            $boundingBox,
            TimeUnit::fromInt(TimeUnit::DAYS),
            LengthUnit::fromInt(LengthUnit::METERS)
        ));

        $name = Name::fromString('Layer 1');
        $description = Description::fromString('Top Layer');
        $layerId = LayerId::fromString($name->slugified());

        /* Interpolation */
        $interpolation = new LayerInterpolationConfiguration();
        $interpolation->addMethod(LayerInterpolationConfiguration::METHOD_GAUSSIAN);
        $interpolation->addMethod(LayerInterpolationConfiguration::METHOD_MEAN);
        $interpolation->setBoundingBox($boundingBox);
        $interpolation->setGridSize($gridSize);

        $pointValues = [
            new PointValue(new Point(-70.5445861, -32.8539223), 1119.0),
            new PointValue(new Point(-70.5720520, -32.8674766), 920.0),
            new PointValue(new Point(-70.5689620, -32.8406544), 931.0),
            new PointValue(new Point(-70.5998611, -32.8374814), 830.0),
            new PointValue(new Point(-70.6163406, -32.8135357), 802.0),
            new PointValue(new Point(-70.6650924, -32.7855427), 727.0),
            new PointValue(new Point(-70.7244873, -32.7454137), 648.0),
            new PointValue(new Point(-70.7644844, -32.7312632), 602.0),
            new PointValue(new Point(-70.5226135, -32.8478655), 1021.0),
            new PointValue(new Point(-70.5384063, -32.8490192), 1061.0),
            new PointValue(new Point(-70.5435562, -32.8420967), 924.0),
            new PointValue(new Point(-70.5469894, -32.8487308), 971.0),
            new PointValue(new Point(-70.5390930, -32.7332849), 1177.0),
            new PointValue(new Point(-70.5524826, -32.7413710), 1026.0),
            new PointValue(new Point(-70.6544494, -32.7405046), 717.0),
            new PointValue(new Point(-70.7430267, -32.7139330), 671.0),
            new PointValue(new Point(-70.7478332, -32.7228874), 625.0),
            new PointValue(new Point(-70.6980514, -32.7329961), 672.0),
            new PointValue(new Point(-70.6819152, -32.7451250), 695.0),
            new PointValue(new Point(-70.6379699, -32.7552311), 745.0),
            new PointValue(new Point(-70.6036376, -32.7563860), 788.0),
            new PointValue(new Point(-70.5741119, -32.7560973), 863.0),
            new PointValue(new Point(-70.5521392, -32.7581184), 955.0),
            new PointValue(new Point(-70.5590057, -32.7927582), 870.0),
            new PointValue(new Point(-70.5895614, -32.7820790), 812.0),
            new PointValue(new Point(-70.6307601, -32.7731306), 766.0),
            new PointValue(new Point(-70.6688690, -32.7607168), 714.0),
            new PointValue(new Point(-70.7186508, -32.7425261), 649.0),
            new PointValue(new Point(-70.7450866, -32.7335737), 622.0),
            new PointValue(new Point(-70.7567596, -32.7280863), 611.0),
            new PointValue(new Point(-70.7200241, -32.7598507), 660.0),
            new PointValue(new Point(-70.7179641, -32.7725533), 667.0),
            new PointValue(new Point(-70.7145309, -32.7924696), 700.0),
            new PointValue(new Point(-70.7114410, -32.8092074), 701.0),
            new PointValue(new Point(-70.7076644, -32.8348852), 716.0),
            new PointValue(new Point(-70.7056045, -32.8519034), 727.0),
            new PointValue(new Point(-70.7042312, -32.8591135), 736.0),
            new PointValue(new Point(-70.7038879, -32.8668998), 739.0),
            new PointValue(new Point(-70.6915283, -32.8706485), 742.0),
            new PointValue(new Point(-70.6702423, -32.8784338), 805.0),
            new PointValue(new Point(-70.6427764, -32.8853535), 841.0),
            new PointValue(new Point(-70.6214904, -32.8888131), 814.0),
            new PointValue(new Point(-70.5727386, -32.8741087), 932.0),
            new PointValue(new Point(-70.6008911, -32.8738204), 834.0),
            new PointValue(new Point(-70.6287002, -32.8787221), 798.0),
            new PointValue(new Point(-70.6431198, -32.8755505), 783.0),
            new PointValue(new Point(-70.6750488, -32.8671882), 750.0),
            new PointValue(new Point(-70.6932449, -32.8516150), 731.0),
            new PointValue(new Point(-70.6973648, -32.8152669), 712.0),
            new PointValue(new Point(-70.7049179, -32.7806358), 687.0),
            new PointValue(new Point(-70.6956481, -32.7835222), 699.0),
            new PointValue(new Point(-70.6843185, -32.7924696), 715.0),
            new PointValue(new Point(-70.6877517, -32.8011275), 718.0),
            new PointValue(new Point(-70.6863784, -32.8149783), 723.0),
            new PointValue(new Point(-70.6753921, -32.8429620), 739.0),
            new PointValue(new Point(-70.6654357, -32.8530571), 751.0),
            new PointValue(new Point(-70.6386566, -32.8631510), 777.0),
            new PointValue(new Point(-70.5905914, -32.8628626), 832.0),
            new PointValue(new Point(-70.5899047, -32.8490192), 831.0),
            new PointValue(new Point(-70.6050109, -32.8484423), 813.0),
            new PointValue(new Point(-70.6317901, -32.8392121), 788.0),
            new PointValue(new Point(-70.6506729, -32.8282501), 765.0),
            new PointValue(new Point(-70.6599426, -32.8158440), 751.0),
            new PointValue(new Point(-70.6630325, -32.8040133), 744.0),
            new PointValue(new Point(-70.6379699, -32.8109387), 776.0)
        ];

        foreach ($pointValues as $pointValue) {
            $interpolation->addPointValue($pointValue);
        }

        $result = $this->container->get('inowas.soilmodel.interpolation_service')->interpolate($interpolation);
        $top = Top::from2DArray($result->result());

        $layer = Layer::fromParams(
            $layerId,
            $name,
            $description,
            LayerNumber::fromInt(0),
            $top,
            Botm::fromValue(590),
            Hk::fromValue(0.864),
            Hani::fromValue(1),
            Vka::fromValue(10),
            Layavg::fromInt(Layavg::TYPE_HARMONIC_MEAN),
            Laytyp::fromValue(Laytyp::TYPE_CONVERTIBLE),
            Laywet::fromFloat(Laywet::WETTING_INACTIVE),
            Ss::fromFloat(1e-5),
            Sy::fromFloat(0.2)
        );

        $commandBus->dispatch(AddLayer::forModflowModel($ownerId, $baseModelId, $layer));

        $name = Name::fromString('Layer 1');
        $description = Description::fromString('Top Layer');
        $layerId = LayerId::fromString($name->slugified());

        $layer = Layer::fromParams(
            $layerId,
            $name,
            $description,
            LayerNumber::fromInt(1),
            Top::fromValue(590),
            Botm::fromValue(550),
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

        /*
         *  CHD-Boundaries
        */
        /** @var ConstantHeadBoundary $chd */
        $chd = ConstantHeadBoundary::createWithParams(
            Name::fromString('Chd-West'),
            Geometry::fromLineString(new LineString([
                [ -70.761468152216395, -32.742437320686882 ],
                [ -70.739900752075556, -32.713424032402173 ]
            ])),
            AffectedLayers::fromArray([1]),
            Metadata::create()
        );

        $observationPointId = ObservationPointId::fromString('OP1');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::CONSTANT_HEAD),
            Name::fromString('OP 1'),
            new Point(70.761468152216395, -32.742437320686882, 4326)
        );

        $chd->addObservationPoint($observationPoint);
        $chd->addConstantHeadToObservationPoint(
            $observationPointId,
            ConstantHeadDateTimeValue::fromParams(
                DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2010-01-01')),
                580,
                580
            )
        );

        $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $chd));

        /** @var ConstantHeadBoundary $chd */
        $chd = ConstantHeadBoundary::createWithParams(
            Name::fromString('Chd-East'),
            Geometry::fromLineString(new LineString([
                [ -70.546497000284148, -32.84965766474577 ],
                [ -70.547861570461322, -32.809630272881954 ]
            ])),
            AffectedLayers::fromArray([0,1]),
            Metadata::create()
        );

        $observationPointId = ObservationPointId::fromString('OP1');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::CONSTANT_HEAD),
            Name::fromString('OP 1'),
            new Point(-70.547861570461322, -32.809630272881954, 4326)
        );

        $chd->addObservationPoint($observationPoint);
        $chd->addConstantHeadToObservationPoint(
            $observationPointId,
            ConstantHeadDateTimeValue::fromParams(
                DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2010-01-01')),
                800,
                800
            )
        );

        $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $chd));

        /*
         * Add RiverBoundary
         * RIV
         */
        $riv = RiverBoundary::createWithParams(
            Name::fromString('Rio Primero River'),
            Geometry::fromLineString(new LineString([
                [ -70.546623443616781, -32.834444454633591 ],
                [ -70.552925605995597, -32.827208638569026 ],
                [ -70.56459627706748, -32.825107917776087 ],
                [ -70.575566707875055, -32.826975225147585 ],
                [ -70.583035937361061, -32.833977627790716 ],
                [ -70.58677055210407, -32.831410080154903 ],
                [ -70.593306127904327, -32.823707437247457 ],
                [ -70.60754434661203, -32.814137486968512 ],
                [ -70.631352515598678, -32.801999989053748 ],
                [ -70.64769145509932, -32.793597105881993 ],
                [ -70.672900104614584, -32.77935888717429 ],
                [ -70.733587594188393, -32.76231970740934 ],
                [ -70.748059226317537, -32.745513941065823 ],
                [ -70.75856283028223, -32.738511538422692 ]
            ], 4326)),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInt(0)),
            Metadata::create()
        );

        $observationPointId = ObservationPointId::fromString('op1');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::RIVER),
            Name::fromString('OP 1'),
            new Point(70.546623443616781, -32.834444454633591, 4326)
        );

        /** @var RiverBoundary $riv */
        $riv->addObservationPoint($observationPoint);
        $riv->addRiverStageToObservationPoint(
            $observationPointId,
            RiverDateTimeValue::fromParams(
                DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2010-01-01')),
                1000,
                998,
                200
            )
        );

        $observationPointId = ObservationPointId::fromString('op2');
        $observationPoint = ObservationPoint::fromIdTypeNameAndGeometry(
            $observationPointId,
            BoundaryType::fromString(BoundaryType::RIVER),
            Name::fromString('OP 2'),
            new Point(-70.75856283028223, -32.738511538422692, 4326)
        );

        /** @var RiverBoundary $riv */
        $riv->addObservationPoint($observationPoint);
        $riv->addRiverStageToObservationPoint(
            $observationPointId,
            RiverDateTimeValue::fromParams(
                DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2010-01-01')),
                650,
                648,
                200
            )
        );

        #$commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $riv));

        /*
         * Add Wells for the BaseScenario
         */
        $wells = array(
            array('name', 'point', 'type', 'layer', 'date', 'pumpingRate'),
            array('Public Well 1', new Point(-70.7260, -32.7351, 4326), WellType::TYPE_PUBLIC_WELL, 1, new \DateTimeImmutable('2010-01-01'), -5000),
            array('Public Well 2', new Point(-70.7258, -32.7352, 4326), WellType::TYPE_PUBLIC_WELL, 1, new \DateTimeImmutable('2010-01-01'), -5000),
            array('Public Well 3', new Point(-70.7256, -32.7353, 4326), WellType::TYPE_PUBLIC_WELL, 1, new \DateTimeImmutable('2010-01-01'), -5000),
            array('Public Well 4', new Point(-70.7129, -32.7470, 4326), WellType::TYPE_PUBLIC_WELL, 1, new \DateTimeImmutable('2010-01-01'), -5000),
            array('Public Well 5', new Point(-70.7435, -32.7404, 4326), WellType::TYPE_PUBLIC_WELL, 1, new \DateTimeImmutable('2010-01-01'), -5000)
        );

        $header = null;
        foreach ($wells as $data){
            if (null === $header){
                $header = $data;
                continue;
            }

            $data = array_combine($header, $data);

            /** @var WellBoundary $wellBoundary */
            $wellBoundary = WellBoundary::createWithParams(
                Name::fromString($data['name']),
                Geometry::fromPoint($data['point']),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInt($data['layer'])),
                Metadata::create()->addWellType(WellType::fromString($data['type']))
            );

            echo sprintf("Add well with name %s.\r\n", $data['name']);
            $wellBoundary = $wellBoundary->addPumpingRate(
                WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable($data['date']), $data['pumpingRate'])
            );
            $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $baseModelId, $wellBoundary));
        }

        /* Create calculation and calculate */
        $start = DateTime::fromDateTime(new \DateTime('2010-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2012-12-31'));
        $stressperiods = StressPeriods::create($start, $end, TimeUnit::fromInt(TimeUnit::DAYS));
        $stressperiods->addStressPeriod(StressPeriod::create(0, 365,1,1,true));
        $stressperiods->addStressPeriod(StressPeriod::create(366, 365,1,1,true));

        $commandBus->dispatch(UpdateStressPeriods::of($ownerId, $baseModelId, $stressperiods));
        $ocStressPeriodData = OcStressPeriodData::create()->addStressPeriod(OcStressPeriod::fromParams(0,0, ['save head', 'save drawdown']));
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
            ScenarioAnalysisName::fromString('ScenarioAnalysis: San Felipe'),
            ScenarioAnalysisDescription::fromString('San Felipe')
        ));

        /*
         * Begin add Scenario 0
         */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioIdAndName(
            $scenarioAnalysisId,
            $ownerId,
            $baseModelId,
            $scenarioId,
            Name::fromString('Scenario 0: San Felipe'),
            Description::fromString('Future Prediction for the year 2020'))
        );


        /*
         * Add more Wells
        */
        $wells = array(
            array('name', 'point', 'type', 'layer', 'date', 'pumpingRate'),
            array('Irrigation Well 1', new Point(-70.6899, -32.8144, 4326), WellType::TYPE_INDUSTRIAL_WELL, 1, new \DateTimeImmutable('2010-01-01'), -10000),
            array('Irrigation Well 2', new Point(-70.6609, -32.8268, 4326), WellType::TYPE_INDUSTRIAL_WELL, 1, new \DateTimeImmutable('2010-01-01'), -10000),
            array('Irrigation Well 3', new Point(-70.7325, -32.7695, 4326), WellType::TYPE_INDUSTRIAL_WELL, 1, new \DateTimeImmutable('2010-01-01'), -10000),
            array('Irrigation Well 4', new Point(-70.6924, -32.8392, 4326), WellType::TYPE_INDUSTRIAL_WELL, 1, new \DateTimeImmutable('2010-01-01'), -10000),
            array('Irrigation Well 5', new Point(-70.6693, -32.7480, 4326), WellType::TYPE_INDUSTRIAL_WELL, 1, new \DateTimeImmutable('2010-01-01'), -10000)
        );

        $header = null;
        foreach ($wells as $data){
            if (null === $header){
                $header = $data;
                continue;
            }

            $data = array_combine($header, $data);

            /** @var WellBoundary $wellBoundary */
            $wellBoundary = WellBoundary::createWithParams(
                Name::fromString($data['name']),
                Geometry::fromPoint($data['point']),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInt($data['layer'])),
                Metadata::create()->addWellType(WellType::fromString($data['type']))
            );

            echo sprintf("Add well with name %s.\r\n", $data['name']);
            $wellBoundary = $wellBoundary->addPumpingRate(
                WellDateTimeValue::fromParams(DateTime::fromDateTimeImmutable($data['date']), $data['pumpingRate'])
            );
            $commandBus->dispatch(AddBoundary::forModflowModel($ownerId, $scenarioId, $wellBoundary));
        }

        echo sprintf("Calculate Scenario 0 with id %s.\r\n", $scenarioId->toString());
        $commandBus->dispatch(CalculateModflowModel::forModflowModelWitUserId($ownerId, $scenarioId));
    }
}
