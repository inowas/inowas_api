<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\Hanoi;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\LineString;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Modflow\OcStressPeriod;
use Inowas\Common\Modflow\OcStressPeriodData;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Soilmodel\HydraulicAnisotropy;
use Inowas\Common\Soilmodel\HydraulicConductivityX;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\VerticalHydraulicConductivity;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\ChangeFlowPackage;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelDescription;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelName;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowCalculation\Model\Command\UpdateCalculationPackageParameter;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Id\UserId;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\ModflowBundle\DataFixtures\Scenarios\LoadScenarioBase;
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

ini_set('memory_limit', '2048M');

class HanoiBaseModelOnly extends LoadScenarioBase
{
    public function load()
    {
        $this->loadUsers($this->container->get('fos_user.user_manager'));
        $geoTools = $this->container->get('inowas.geotools.geotools_service');
        $this->createEventStreamTableIfNotExists('event_stream');

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = UserId::fromString($this->ownerId);
        $modelId = ModflowId::generate();

        $area = Area::create(
            BoundaryId::generate(),
            BoundaryName::fromString('Hanoi Area'),
            new Polygon(array(
                array(
                    array(105.790767733626808, 21.094425932026443),
                    array(105.796959843400032, 21.093521487879368),
                    array(105.802017060333782, 21.092234483652170),
                    array(105.808084259744490, 21.090442258424751),
                    array(105.812499379361824, 21.088745285770433),
                    array(105.817189857772419, 21.086246452411380),
                    array(105.821849880920155, 21.083084791161816),
                    array(105.826206685192972, 21.080549811906632),
                    array(105.829745666549428, 21.077143263497668),
                    array(105.833738284468225, 21.073871989488410),
                    array(105.837054371969458, 21.068790508713093),
                    array(105.843156477826938, 21.061619066459148),
                    array(105.845257297050807, 21.058494488216656),
                    array(105.848091064693264, 21.055416254106909),
                    array(105.850415052797018, 21.051740212147806),
                    array(105.853986426189834, 21.047219935885728),
                    array(105.857317797743207, 21.042700799256870),
                    array(105.860886165285677, 21.037730164508108),
                    array(105.862781077291359, 21.033668431680731),
                    array(105.865628458812012, 21.028476242159179),
                    array(105.867512713611035, 21.022613568026749),
                    array(105.869402048566840, 21.017651320651229),
                    array(105.871388782041976, 21.013426442220442),
                    array(105.872849945737570, 21.008166192541132),
                    array(105.876181664767913, 21.003946864458868),
                    array(105.882508712001197, 21.001813076331899),
                    array(105.889491767034770, 21.000288452359857),
                    array(105.894324807327010, 20.997811850332017),
                    array(105.898130162725238, 20.994990356212355),
                    array(105.903035574892471, 20.989098851962478),
                    array(105.905619253163707, 20.984707849769400),
                    array(105.905107309855680, 20.977094091795209),
                    array(105.901707144804220, 20.969670720258843),
                    array(105.896052272867848, 20.959195015805960),
                    array(105.886865167028475, 20.950138230157627),
                    array(105.877901274443431, 20.947208019282808),
                    array(105.834499067698161, 20.951978316227517),
                    array(105.806257646336405, 20.968923300374374),
                    array(105.781856978173835, 21.008608549010258),
                    array(105.768216532593982, 21.039487418417067),
                    array(105.774357585691064, 21.072902571997240),
                    array(105.777062025914603, 21.090749775344797),
                    array(105.783049106327312, 21.093961473086512),
                    array(105.790767733626808, 21.094425932026443)
                )
            ), 4326)
        );
        $gridSize = GridSize::fromXY(165, 175);
        $commandBus->dispatch(CreateModflowModel::newWithId($ownerId, $modelId, $area, $gridSize));
        $commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modelId, Modelname::fromString('Base Scenario Hanoi 2005-2007')));
        $commandBus->dispatch(ChangeModflowModelDescription::forModflowModel(
            $ownerId,
            $modelId,
            ModflowModelDescription::fromString('Calibrated groundwater base model, 2005-2007.'))
        );

        $box = $geoTools->projectBoundingBox(BoundingBox::fromCoordinates(578205, 594692, 2316000, 2333500, 32648), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $soilModelId = SoilmodelId::generate();
        $commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($modelId, $soilModelId));
        $commandBus->dispatch(CreateSoilmodel::byUserWithModelId($ownerId, $soilModelId));
        $commandBus->dispatch(ChangeSoilmodelName::forSoilmodel($ownerId, $soilModelId, SoilmodelName::fromString('Hanoi-Area')));
        $commandBus->dispatch(ChangeSoilmodelDescription::forSoilmodel($ownerId, $soilModelId, SoilmodelDescription::fromString('Soilmodel for Hanoi-Area')));
        $layers = [
            ['Surface Layer', 'silt, silty clay, loam'],
            ['HUA', 'Unconfined aquifer, silt, silty clay, clay, fine sand'],
            ['Impervious Layer', 'Aquitard, clay, silt'],
            ['PCA', 'Confined aquifer, gravel, coarse and middle sand, lenses of silt and clay'],
        ];

        foreach ($layers as $key => $layer) {
            $layerId = GeologicalLayerId::generate();
            $type = Laytyp::fromValue(Laytyp::TYPE_CONVERTIBLE);
            $layerNumber = GeologicalLayerNumber::fromInteger($key);

            $commandBus->dispatch(
                AddGeologicalLayerToSoilmodel::forSoilmodel(
                    $ownerId,
                    $soilModelId,
                    GeologicalLayer::fromParams(
                        $layerId,
                        $type,
                        $layerNumber,
                        GeologicalLayerName::fromString($layer[0]),
                        GeologicalLayerDescription::fromString($layer[1])
                    )
                )
            );

            if ($key === 0) {
                /* Load Top-Elevation for the first layer */
                echo sprintf("Load top-elevation %s Memory usage\r\n", memory_get_usage());
                $string = file_get_contents(__DIR__ . "/extracted/top.json");
                $topElevation = TopElevation::fromLayerValue(json_decode($string, true));
                $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $topElevation));
            }

            /* Load Bottom-Elevation for all layers */
            echo sprintf("Load bottom-elevation %s Memory usage\r\n", memory_get_usage());
            $string = file_get_contents(__DIR__ . "/extracted/botm.json");
            $bottomElevation = BottomElevation::fromLayerValue(json_decode($string, true)[$layerNumber->toInteger()]);
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $bottomElevation));

            /* Load Hk for all layers */
            echo sprintf("Load Hydraulic Conductivity. %s Memory usage\r\n", memory_get_usage());
            $string = file_get_contents(__DIR__ . "/extracted/hk.json");
            $hk = HydraulicConductivityX::fromLayerValue(json_decode($string, true)[$layerNumber->toInteger()]);
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $hk));

            /* Load Hydraulic Anisotropy for all layers */
            echo sprintf("Load Hydraulic Anisotropy. %s Memory usage\r\n", memory_get_usage());
            $ha = HydraulicAnisotropy::fromLayerValue(1.0);
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $ha));

            /* Load Vertical Conductivity for all layers */
            echo sprintf("Load vertical Hydraulic Conductivity. %s Memory usage\r\n", memory_get_usage());
            $string = file_get_contents(__DIR__ . "/extracted/vka.json");
            $vka = VerticalHydraulicConductivity::fromLayerValue(json_decode($string, true)[$layerNumber->toInteger()]);
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $vka));

            /* Load Specific Storage for all layers */
            echo sprintf("Load Specific Storage. %s Memory usage\r\n", memory_get_usage());
            $string = file_get_contents(__DIR__ . "/extracted/ss.json");
            $ss = SpecificStorage::fromLayerValue(json_decode($string, true)[$layerNumber->toInteger()]);
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $ss));

            /* Load Specific Yield for all layers */
            echo sprintf("Load Specific Yield. %s Memory usage\r\n", memory_get_usage());
            $string = file_get_contents(__DIR__ . "/extracted/sy.json");
            $sy = SpecificYield::fromLayerValue(json_decode($string, true)[$layerNumber->toInteger()]);
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, $sy));
        }

        /*
         * Add Wells for the BaseScenario
         */
        $fileName = __DIR__ . "/data/wells_basecase.csv";
        $wells = $this->loadRowsFromCsv($fileName);
        $header = $this->loadHeaderFromCsv($fileName);
        $dates = $this->getDates($header);

        foreach ($wells as $key => $well) {

            $wellBoundary = WellBoundary::createWithParams(
                BoundaryId::generate(),
                BoundaryName::fromString($well['Name']),
                Geometry::fromPoint($geoTools->projectPoint(new Point($well['x'], $well['y'], $well['srid']), Srid::fromInt(4326))),
                WellType::fromString($well['type']),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger((int)$well['layer'] - 1))
            );

            $value = null;
            foreach ($dates as $date) {
                if (is_numeric($well[$date])) {
                    if ($well[$date] !== $value) {
                        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(
                            new \DateTimeImmutable(explode(':', $date)[1]), (float)$well[$date]
                        ));
                    }
                    $value = $well[$date];
                }
            }

            echo sprintf('Add Well %s to BaseModel' . "\r\n", $wellBoundary->name()->toString());
            $commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $wellBoundary));
        }

        /*
         * Add River for the baseScenario
         */
        $riverPoints = $this->loadRowsFromCsv(__DIR__ . "/data/river_geometry_basecase.csv");
        foreach ($riverPoints as $key => $point) {
            $riverPoints[$key] = $geoTools->projectPoint(new Point($point['x'], $point['y'], $point['srid']), Srid::fromInt(4326));
        }

        /** @var RiverBoundary $river */
        $river = RiverBoundary::createWithParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Red River'),
            Geometry::fromLineString(new LineString($riverPoints, 4326))
        );

        $observationPoints = $this->loadRowsFromCsv(__DIR__ . "/data/river_stages_basecase.csv");
        $header = $this->loadHeaderFromCsv(__DIR__ . "/data/river_stages_basecase.csv");
        $dates = $this->getDates($header);

        foreach ($observationPoints as $op) {
            $observationPoint = ObservationPoint::fromIdNameAndGeometry(
                ObservationPointId::generate(),
                ObservationPointName::fromString($op['name']),
                Geometry::fromPoint($geoTools->projectPoint(new Point($op['x'], $op['y'], $op['srid']), Srid::fromInt(4326)))
            );

            foreach ($dates as $date) {
                if (is_numeric($op[$date])) {
                    $observationPoint = $observationPoint->addDateTimeValue(
                        RiverDateTimeValue::fromParams(
                            new \DateTimeImmutable(explode(':', $date)[1]), $op[$date], 0, 1500)
                    );
                }
            }
            echo sprintf("Add River-Boundary ObservationPoint %s.\r\n", $observationPoint->name()->toString());
            $river = $river->addObservationPoint($observationPoint);
        }
        $commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $river));

        /*
         * Add ConstantHead for the baseScenario
         */
        $chdPoints = $this->loadRowsFromCsv(__DIR__ . "/data/chd_geometry_basecase.csv");
        foreach ($chdPoints as $key => $point) {
            $chdPoints[$key] = $geoTools->projectPoint(new Point($point['x'], $point['y'], $point['srid']), Srid::fromInt(4326));
        }

        /** @var ConstantHeadBoundary $chdBoundary */
        $chdBoundary = ConstantHeadBoundary::createWithParams(
            BoundaryId::generate(),
            BoundaryName::fromString('ChdBoundary'),
            Geometry::fromLineString(new LineString($chdPoints, 4326)),
            AffectedLayers::createWithLayerNumbers(array(
                    LayerNumber::fromInteger(2),
                    LayerNumber::fromInteger(3)
                )
            )
        );

        $observationPoints = $this->loadRowsFromCsv(__DIR__ . "/data/chd_stages_basecase.csv");
        $header = $this->loadHeaderFromCsv(__DIR__ . "/data/chd_stages_basecase.csv");
        $dates = $this->getDates($header);

        foreach ($observationPoints as $op) {

            $observationPointId = ObservationPointId::generate();
            $observationPoint = ObservationPoint::fromIdNameAndGeometry(
                $observationPointId,
                ObservationPointName::fromString($op['name']),
                Geometry::fromPoint($geoTools->projectPoint(new Point($op['x'], $op['y'], $op['srid']), Srid::fromInt(4326)))
            );

            echo sprintf("Add Chd-Boundary ObservationPoint %s.\r\n", $observationPoint->name()->toString());
            $chdBoundary = $chdBoundary->addObservationPoint($observationPoint);

            foreach ($dates as $date) {
                $chdBoundary = $chdBoundary->addConstantHeadToObservationPoint($observationPointId, ConstantHeadDateTimeValue::fromParams(
                    new \DateTimeImmutable(explode(':', $date)[1]),
                    $op[$date],
                    $op[$date]
                ));
            }
        }
        echo sprintf("Add Chd-Boundary %s.\r\n", $chdBoundary->name()->toString());
        $commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $chdBoundary));

        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2007-12-31'));

        echo sprintf("Dispatch CreateModflowModelCalculation %s.\r\n", $calculationId->toString());
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modelId, $start, $end));

        echo sprintf("Dispatch ChangeFlowPackage to Upw %s.\r\n", $calculationId->toString());
        $commandBus->dispatch(ChangeFlowPackage::byUserWithCalculationId($ownerId, $calculationId, PackageName::fromString('upw')));

        echo sprintf("Dispatch UpdateCalculationPackageParameter %s.\r\n", $calculationId->toString());
        $commandBus->dispatch(UpdateCalculationPackageParameter::byUserWithModelId($calculationId, $ownerId, $modelId, 'upw', 'layTyp', Laytyp::fromInt(1)));

        echo sprintf("Dispatch UpdateCalculationPackageParameter %s.\r\n", $calculationId->toString());
        $ocStressPeriodData = OcStressPeriodData::create()->addStressPeriod(OcStressPeriod::fromParams(0, 0, ['save head', 'save drawdown']));
        $commandBus->dispatch(UpdateCalculationPackageParameter::byUserWithModelId($calculationId, $ownerId, $modelId, 'oc', 'ocStressPeriodData', $ocStressPeriodData));

        echo sprintf("Dispatch CalculateModflowModelCalculation %s.\r\n", $calculationId->toString());
        $commandBus->dispatch(CalculateModflowModelCalculation::byUserWithModelId($ownerId, $calculationId, $modelId));
    }
}
