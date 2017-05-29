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
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\OcStressPeriod;
use Inowas\Common\Modflow\OcStressPeriodData;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\TimeUnit;
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
use Inowas\ModflowModel\Model\Command\UpdateBoundaryGeometry;
use Inowas\ModflowCalculation\Model\Command\UpdateCalculationPackageParameter;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Id\UserId;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\ModflowBundle\DataFixtures\Scenarios\LoadScenarioBase;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenario;
use Inowas\ScenarioAnalysis\Model\Command\CreateScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisDescription;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisName;
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

class Hanoi extends LoadScenarioBase
{
    public function load()
    {
        $this->loadUsers($this->container->get('fos_user.user_manager'));
        $geoTools = $this->container->get('inowas.geotools.geotools_service');
        $this->createEventStreamTableIfNotExists('event_stream');

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = UserId::fromString($this->ownerId);
        $modelId = ModflowId::generate();
        $area = Area::create(BoundaryId::generate(), BoundaryName::fromString('Hanoi Area'), new Polygon(array(
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
        ), 4326));
        $gridSize = GridSize::fromXY(165, 175);
        $commandBus->dispatch(CreateModflowModel::newWithIdAndUnits($ownerId, $modelId, $area, $gridSize, TimeUnit::fromString(TimeUnit::DAYS), LengthUnit::fromString(LengthUnit::METERS)));
        $commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modelId, ModelName::fromString('Base Scenario Hanoi 2005-2007')));
        $commandBus->dispatch(ChangeModflowModelDescription::forModflowModel(
            $ownerId,
            $modelId,
            ModelDescription::fromString('Calibrated groundwater base model, 2005-2007.')));

        $box = $geoTools->projectBoundingBox(BoundingBox::fromCoordinates(578205, 594692, 2316000, 2333500, 32648), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $soilModelId = SoilmodelId::generate();
        $commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($ownerId, $modelId, $soilModelId));
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
        $boreholes = array(
            array('name', 'x', 'y', 'top', 'bot_0', 'bot_1', 'bot_2', 'bot_3', 'kx_0', 'ky_0', 'kz_0', 'kx_1', 'ky_1', 'kz_1', 'kx_2', 'ky_2', 'kz_2', 'kx_3', 'ky_3', 'kz_3'),
            array('SC2_GU1', 11771882.34, 2392544.12, 4.55, 3, -37.44, -38.45, -71.95,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU2', 11789082.18, 2389714.82, 8.15, -1, -19.84, -20.85, -107.85,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU3', 11778857.62, 2391711.98, 6.97, 1, -18.03, -31.03, -65.03,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU4', 11784193.77, 2394196.31, 9.26, -1.75, -11.75, -13.75, -66.25,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU5', 11781568.57, 2392545.18, 5.45, 0.45, -24.55, -38.05, -75.55,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0001, 0.0001, 0.0000, 40.0000, 40.0000, 4.0000),
            array('SC2_GU6', 11777013.85, 2400404.42, 9.25, -1.4, -20.75, -26.75, -68.75,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU7', 11783051.08, 2395101.75, 3.21, -11.79, -34.59, -38.29, -90.79,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU8', 11777309.40, 2390254.19, 6.41, 1, -39.58, -40.59, -73.59,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 7.0000, 7.0000, 0.7000),
            array('SC2_GU9', 11784512.97, 2393046.65, 8.34, -0.66, -25.65, -26.66, -66.66,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU10', 11778452.98, 2390393.68, 6.3, 2, -7.7, -32.2, -71.7,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 7.0000, 7.0000, 0.7000),
            array('SC2_GU11', 11778745.08, 2399607.08, 7.48, -1, -13.51, -14.52, -84.02,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0100, 0.0100, 0.0010, 50.0000, 50.0000, 5.0000),
            array('SC2_GU12', 11778807.54, 2396471.02, 6.78, -3.73, -30.72, -31.73, -65.23,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU13', 11772850.45, 2386662.03, 5.05, 1, -9.95, -29.95, -59.95,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU14', 11781833.25, 2394756.26, 6.87, 2, -7.33, -13.13, -71.13,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 70.0000, 70.0000, 7.0000),
            array('SC2_GU15', 11785022.12, 2395765.18, 11.47, -1, -8.92, -9.93, -113.13,10.0000, 10.0000, 1.0000, 40.0000, 40.0000, 4.0000, 0.2000, 0.2000, 0.0200, 40.0000, 40.0000, 4.0000),
            array('SC2_GU16', 11775146.75, 2398847.69, 5.86, 0.2, -5.64, -24.14, -48.14,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU17', 11781244.76, 2397032.49, 6.88, 2, -25.21, -26.22, -81.32,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU18', 11777209.26, 2402770.9, 8.52, 0.7, -17.47, -18.48, -81.48,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU19', 11783628.45, 2390521.59, 5.51, -7.09, -24.99, -37.99, -79.99,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU20', 11787952.59, 2391352.68, 10.23, -1, -3.17, -13.77, -60.27,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU21', 11772535.98, 2391516.61, 5.06, -1.44, -21.44, -34.64, -88.54,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU22', 11779155.03, 2396640.9, 7.71, -1.5, -24.29, -27.79, -47.29,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU23', 11760714.53, 2397939.64, 6.03, 1, -18.47, -32.47, -66.07,30.0000, 30.0000, 3.0000, 40.0000, 40.0000, 4.0000, 0.0001, 0.0001, 0.0000, 10.0000, 10.0000, 1.0000),
            array('SC2_GU24', 11774649.17, 2399215.18, 6.51, 3, -25.5, -30.5, -49.5,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU25', 11782792.24, 2384025.09, 5.26, 1.76, -21.34, -33.64, -59.24,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0000, 0.0000, 0.0000, 40.0000, 40.0000, 4.0000),
            array('SC2_GU26', 11780072.96, 2396064.94, 6.41, -3.19, -24.09, -31.09, -49.09,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU27', 11777813.99, 2386822.58, 4, 0.2, -19.9, -33.2, -59.2,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU28', 11786910.46, 2387406.18, 7.51, 2, -15.99, -32.29, -56.49,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU29', 11788382.99, 2388557.67, 8.46, 0.86, -37.54, -41.74, -62.74,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU30', 11781544.58, 2399809.73, 9.92, 1, -19.09, -24.09, -55.99,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU31', 11779912.77, 2401723.79, 10.27, 0.5, -11.52, -12.53, -55.03,10.0000, 10.0000, 1.0000, 15.0000, 15.0000, 1.5000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU32', 11778716.08, 2402222.88, 7.98, 0.2, -1, -7.02, -58.02,10.0000, 10.0000, 1.0000, 15.0000, 15.0000, 1.5000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU33', 11782681.56, 2398443.64, 12.6, -4.4, -13.9, -16.4, -55.2,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU34', 11782711.76, 2383219.36, 5.72, 1, -14.28, -35.58, -92.28,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU35', 11782877.61, 2387087.35, 4.855, -10.15, -18.15, -33.15, -59.15,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0000, 0.0000, 0.0000, 20.0000, 20.0000, 2.0000),
            array('SC2_GU36', 11780837.05, 2392172.81, 5.81, 0.5, -6.19, -37.19, -49.09,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0001, 0.0001, 0.0000, 40.0000, 40.0000, 4.0000),
            array('SC2_GU37', 11775298.68, 2396584.49, 6.92, 0.5, 0.92, -12.58, -53.08,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU38', 11771588.05, 2400278.11, 7.238, -8.76, -17.76, -26.76, -50.96,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU39', 11786863.07, 2387774.93, 7.86, -4.14, -15.64, -31.14, -58.54,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU40', 11785494.76, 2387728.64, 5.58, -2, -23.42, -36.22, -52.92,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 20.0000, 20.0000, 2.0000),
            array('SC2_GU41', 11785359.45, 2388446.5, 5.24, -1, -24.76, -29.76, -56.76,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 20.0000, 20.0000, 2.0000),
            array('SC2_GU42', 11783834.59, 2393475.89, 6.67, 0.47, -7.83, -25.33, -66.83,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU43', 11778042.14, 2393748.39, 6.72, -0.58, -24.77, -25.78, -66.28,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU44', 11774767.25, 2397966.02, 7.18, -0.5, -18.32, -19.32, -58.82,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU45', 11778459.29, 2399719.84, 6.87, -2, -23.72, -24.73, -72.13,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0100, 0.0100, 0.0010, 10.0000, 10.0000, 1.0000),
            array('SC2_GU46', 11779001.31, 2392931.32, 6.42, -2, -22.57, -23.58, -72.48,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU47', 11787253.39, 2398790.31, 7.32, -11.18, -26.37, -27.38, -72.68,10.0000, 10.0000, 1.0000, 40.0000, 40.0000, 4.0000, 0.2000, 0.2000, 0.0200, 40.0000, 40.0000, 4.0000),
            array('SC2_GU48', 11779321.83, 2394682.34, 6.25, -1, -25.75, -27.55, -55.75,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU49', 11788397.99, 2396629.41, 4.6, -5.1, -16.69, -17.7, -77.7,10.0000, 10.0000, 1.0000, 40.0000, 40.0000, 4.0000, 0.2000, 0.2000, 0.0200, 40.0000, 40.0000, 4.0000),
            array('SC2_GU50', 11776362.25, 2398212.2, 6.97, 0.47, -20.52, -21.53, -49.03,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU51', 11780153.2, 2399710.99, 6.44, -1.7, -16.55, -17.56, -68.56,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU52', 11775049.53, 2401787.45, 6.88, -1.4, -22.12, -26.52, -78.72,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU53', 11773006.52, 2397389.94, 5.98, 2, -32.01, -33.02, -63.02,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU54', 11775636.91, 2391945.87, 4.61, 1.01, -18.39, -22.89, -80.99,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0100, 0.0100, 0.0010, 10.0000, 10.0000, 1.0000),
            array('SC2_GU55', 11782808.67, 2397713.81, 10.49, 3, -24.11, -30.01, -75.51,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU56', 11782239.75, 2397303.54, 13.11, 2, -17.88, -18.89, -69.19,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU57', 11778341.11, 2386909.75, 5.35, -2.65, -39.65, -44.65, -70.85,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU58', 11777301.77, 2396625.85, 6.62, 0.3, -19.38, -20.38, -65.88,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0100, 0.0100, 0.0010, 50.0000, 50.0000, 5.0000),
            array('SC2_GU59', 11778384.57, 2397052.79, 7, 2, -29.99, -31, -68.6,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU60', 11781117.72, 2394046.04, 7.19, -9.41, -28.3, -29.31, -69.31,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 70.0000, 70.0000, 7.0000),
            array('SC2_GU61', 11781602.56, 2395825.27, 7.82, 3, -25.17, -26.18, -56.18,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 70.0000, 70.0000, 7.0000),
            array('SC2_GU62', 11784169.97, 2395592.91, 9.66, 2, -11.34, -27.34, -102.34,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU63', 11781035.36, 2397295.63, 7.09, -3, -19.9, -20.91, -85.11,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU64', 11782599.06, 2394228.74, 6.05, 3, -2.95, -25.45, -80.95,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 70.0000, 70.0000, 7.0000),
            array('SC2_GU65', 11784219.85, 2393183.29, 5.25, 2, -1.75, -29.75, -72.75,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU66', 11784938.17, 2393227.68, 10.54, -1.2, -9.45, -10.46, -80.46,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU67', 11782485.51, 2392841.91, 5.52, 3, -14.48, -31.48, -79.48,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU68', 11783589.92, 2392750.53, 5.59, 1.09, -24.41, -25.41, -72.41,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.0010, 0.0010, 0.0001, 50.0000, 50.0000, 5.0000),
            array('SC2_GU69', 11777852.62, 2384147.89, 5.79, 3, -31.71, -37.21, -62.21,10.0000, 10.0000, 1.0000, 10.0000, 10.0000, 1.0000, 0.1000, 0.1000, 0.0100, 30.0000, 30.0000, 3.0000),
            array('SC2_GU70', 11778108.29, 2391057.06, 5.63, 0.13, -29.37, -36.37, -68.37,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 7.0000, 7.0000, 0.7000),
            array('SC2_GU71', 11783363.26, 2390073.25, 5.5, 0.5, -22.5, -48, -79.5,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU72', 11778272.54, 2397633.23, 7.48, -1, -14.51, -15.52, -64.22,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU73', 11771647.38, 2392411.6, 5.45, -4.55, -26.55, -30.55, -78.55,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU74', 11776094.56, 2389455.36, 5.7, 0.5, -11.3, -33.3, -76.8,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU75', 11788517.19, 2390860.62, 9.1, -2, -13.89, -14.9, -61.4,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU76', 11777147.1, 2402553.12, 7.57, -0.43, -21.42, -22.43, -81.43,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU77', 11786277.06, 2394518.28, 10.4, -0.5, -9.1, -20.1, -104.6,10.0000, 10.0000, 1.0000, 40.0000, 40.0000, 4.0000, 0.2000, 0.2000, 0.0200, 40.0000, 40.0000, 4.0000),
            array('SC2_GU78', 11785882.41, 2389731.92, 5.83, -1, -17.67, -33.17, -86.97,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 20.0000, 20.0000, 2.0000),
            array('SC2_GU79', 11775388.22, 2394326.9, 5.09, -6.91, -28.9, -29.91, -60.91,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU80', 11783396.36, 2390930.73, 5.91, 3, -24.59, -37.59, -79.59,10.0000, 10.0000, 1.0000, 0.2000, 0.2000, 0.0200, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU81', 11783318.42, 2379920.67, 4.33, -0.67, -33.66, -34.67, -86.67,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 40.0000, 40.0000, 4.0000),
            array('SC2_GU82', 11770462, 2403116.55, 9.89, -2.5, -12.1, -13.11, -65.11,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU83', 11783103.2, 2397142.16, 11.68, 3, -21.32, -23.82, -84.32,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU84', 11776546.92, 2391893.9, 6.42, 2.62, -33.58, -34.58, -66.78,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0010, 0.0010, 0.0001, 10.0000, 10.0000, 1.0000),
            array('SC2_GU85', 11780517.15, 2385713.39, 5.61, -1.89, -20.69, -39.39, -60.39,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0000, 0.0000, 0.0000, 20.0000, 20.0000, 2.0000),
            array('SC2_GU86', 11782769.78, 2387640.47, 5.23, 2, -24.77, -37.17, -59.77,10.0000, 10.0000, 1.0000, 7.0000, 7.0000, 0.7000, 0.0000, 0.0000, 0.0000, 20.0000, 20.0000, 2.0000),
            array('SC2_GU87', 11776760.16, 2404465.83, 9.98, -2, -20.02, -29.02, -76.32,20.0000, 20.0000, 2.0000, 15.0000, 15.0000, 1.5000, 0.1000, 0.1000, 0.0100, 50.0000, 50.0000, 5.0000),
            array('SC2_GU88', 11766470.1, 2391498.39, 7.716, -3.78, -29.77, -30.78, -60.58,30.0000, 30.0000, 3.0000, 40.0000, 40.0000, 4.0000, 0.1000, 0.1000, 0.0100, 10.0000, 10.0000, 1.0000),
            array('SC2_GU89', 11775192.52, 2388842.32, 4.719, -23.28, -36.27, -37.28, -76.28,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000),
            array('SC2_GU90', 11772988.05, 2386432.76, 5.43, -9.57, -38.06, -39.07, -65.97,10.0000, 10.0000, 1.0000, 20.0000, 20.0000, 2.0000, 0.0010, 0.0010, 0.0001, 40.0000, 40.0000, 4.0000)
        );
        $header = null;
        foreach ($boreholes as $key => $borehole) {
            if (is_null($header)) {
                $header = $borehole;
                continue;
            }

            $borehole = array_combine($header, $borehole);
            echo sprintf("Add BoreHole %s to soilmodel %s.\r\n", $borehole['name'], $soilModelId->toString());

            $boreLogId = BoreLogId::generate();
            $boreLogName = BoreLogName::fromString($borehole['name']);

            $point = $geoTools->projectPoint(new Point($borehole['x'], $borehole['y'], 3857), Srid::fromInt(4326));
            $boreLogLocation = BoreLogLocation::fromPoint(new Point($point->getX(), $point->getY()));
            $commandBus->dispatch(CreateBoreLog::byUser($ownerId, $boreLogId, $boreLogName, $boreLogLocation));

            $horizon = Horizon::fromParams(
                HorizonId::generate(),
                GeologicalLayerNumber::fromInteger(0),
                HTop::fromMeters($borehole['top']),
                HBottom::fromMeters($borehole['bot_0']),
                Conductivity::fromXYZinMPerDay(
                    HydraulicConductivityX::fromPointValue($borehole['kx_0']),
                    HydraulicConductivityY::fromPointValue($borehole['ky_0']),
                    HydraulicConductivityZ::fromPointValue($borehole['kz_0'])
                ),
                Storage::fromParams(
                    SpecificStorage::fromPointValue(1e-5),
                    SpecificYield::fromPointValue(0.2)
                )
            );
            $commandBus->dispatch(AddHorizonToBoreLog::byUserWithId($ownerId, $boreLogId, $horizon));

            $horizon = Horizon::fromParams(
                HorizonId::generate(),
                GeologicalLayerNumber::fromInteger(1),
                HTop::fromMeters($borehole['bot_0']),
                HBottom::fromMeters($borehole['bot_1']),
                Conductivity::fromXYZinMPerDay(
                    HydraulicConductivityX::fromPointValue($borehole['kx_1']),
                    HydraulicConductivityY::fromPointValue($borehole['ky_1']),
                    HydraulicConductivityZ::fromPointValue($borehole['kz_1'])
                ),
                Storage::fromParams(
                    SpecificStorage::fromPointValue(1e-5),
                    SpecificYield::fromPointValue(0.25)
                )
            );
            $commandBus->dispatch(AddHorizonToBoreLog::byUserWithId($ownerId, $boreLogId, $horizon));

            $horizon = Horizon::fromParams(
                HorizonId::generate(),
                GeologicalLayerNumber::fromInteger(2),
                HTop::fromMeters($borehole['bot_1']),
                HBottom::fromMeters($borehole['bot_2']),
                Conductivity::fromXYZinMPerDay(
                    HydraulicConductivityX::fromPointValue($borehole['kx_2']),
                    HydraulicConductivityY::fromPointValue($borehole['ky_2']),
                    HydraulicConductivityZ::fromPointValue($borehole['kz_2'])
                ),
                Storage::fromParams(
                    SpecificStorage::fromPointValue(1e-5),
                    SpecificYield::fromPointValue(0.03)
                )
            );
            $commandBus->dispatch(AddHorizonToBoreLog::byUserWithId($ownerId, $boreLogId, $horizon));

            $horizon = Horizon::fromParams(
                HorizonId::generate(),
                GeologicalLayerNumber::fromInteger(3),
                HTop::fromMeters($borehole['bot_2']),
                HBottom::fromMeters($borehole['bot_3']),
                Conductivity::fromXYZinMPerDay(
                    HydraulicConductivityX::fromPointValue($borehole['kx_3']),
                    HydraulicConductivityY::fromPointValue($borehole['ky_3']),
                    HydraulicConductivityZ::fromPointValue($borehole['kz_3'])
                ),
                Storage::fromParams(
                    SpecificStorage::fromPointValue(0.0004),
                    SpecificYield::fromPointValue(0.15)
                )
            );
            $commandBus->dispatch(AddHorizonToBoreLog::byUserWithId($ownerId, $boreLogId, $horizon));
            $commandBus->dispatch(AddBoreLogToSoilmodel::byUserWithId($ownerId, $soilModelId, $boreLogId));
        }
        */
        #echo sprintf("Interpolate soilmodel with %s Memory usage\r\n", memory_get_usage());
        #$commandBus->dispatch(InterpolateSoilmodel::forSoilmodel($ownerId, $soilModelId, $boundingBox, $gridSize));

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
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger((int)$well['layer']-1))
            );

            $value = null;
            foreach ($dates as $date){
                if (is_numeric($well[$date])){
                    if ($well[$date] !== $value){
                        $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(
                            new \DateTimeImmutable(explode(':', $date)[1]), (float)$well[$date]
                        ));
                    }
                    $value = $well[$date];
                }
            }

            echo sprintf('Add Well %s to BaseModel'."\r\n", $wellBoundary->name()->toString());
            $commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $wellBoundary));
        }

        /*
         * Add River for the baseScenario
         */
        $riverPoints = $this->loadRowsFromCsv(__DIR__ . "/data/river_geometry_basecase.csv");
        foreach ($riverPoints as $key => $point){
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

        foreach ($observationPoints as $op){
            $observationPoint = ObservationPoint::fromIdNameAndGeometry(
                ObservationPointId::generate(),
                ObservationPointName::fromString($op['name']),
                Geometry::fromPoint($geoTools->projectPoint(new Point($op['x'], $op['y'], $op['srid']), Srid::fromInt(4326)))
            );

            foreach ($dates as $date){
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
        foreach ($chdPoints as $key => $point){
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

        foreach ($observationPoints as $op){

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

        $calculationList = [];
        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2007-12-31'));
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modelId, $start, $end));
        $calculationList[] = [$calculationId, $ownerId, $modelId];

        /* ------- */

        /*
         * Begin add Scenario 1
         */
        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $commandBus->dispatch(CreateScenarioAnalysis::byUserWithBaseModelNameAndDescription(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('ScenarioAnalysis: Hanoi 2005-2007'),
            ScenarioAnalysisDescription::fromString('ScenarioAnalysis: Hanoi 2005-2007')
        ));

        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioId(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            $scenarioId,
            ModelName::fromString('Scenario 1'),
            ModelDescription::fromString('Simulation of MAR type river bank filtration'))
        );
        $boundariesFinder = $this->container->get('inowas.modflowmodel.boundaries_finder');
        $rbfRelocatedWellNamesAndGeometry = array(
            'H07_6' => $geoTools->projectPoint(new Point(588637, 2326840, 32648), Srid::fromInt(4326)),
            'H10_6' => $geoTools->projectPoint(new Point(589150, 2326214, 32648), Srid::fromInt(4326)),
            'H11_8' => $geoTools->projectPoint(new Point(593446, 2321044, 32648), Srid::fromInt(4326)),
            'H19_6' => $geoTools->projectPoint(new Point(589050, 2326431, 32648), Srid::fromInt(4326)),
            'H2_1'  => $geoTools->projectPoint(new Point(584451, 2331823, 32648), Srid::fromInt(4326)),
            'H2_8'  => $geoTools->projectPoint(new Point(593249, 2321333, 32648), Srid::fromInt(4326)),
            'H5_1'  => $geoTools->projectPoint(new Point(588440, 2327043, 32648), Srid::fromInt(4326)),
            'H8_6'  => $geoTools->projectPoint(new Point(588829, 2326631, 32648), Srid::fromInt(4326)),
            'H8_8'  => $geoTools->projectPoint(new Point(593443, 2321233, 32648), Srid::fromInt(4326)),
            'H9_1'  => $geoTools->projectPoint(new Point(584649, 2331729, 32648), Srid::fromInt(4326))
        );
        foreach ($rbfRelocatedWellNamesAndGeometry as $name => $geometry) {
            /** @var BoundaryId[] $boundaryIds */
            $boundaryIds = $boundariesFinder->getBoundaryIdsByName($scenarioId, BoundaryName::fromString($name));
            if (count($boundaryIds)==0){continue;}
            echo sprintf("Move Well %s.\r\n", $name);
            $boundaryId = $boundaryIds[0];
            $geometry = Geometry::fromPoint($geometry);
            $commandBus->dispatch(UpdateBoundaryGeometry::byUser($ownerId, $scenarioId, $boundaryId, $geometry));
        }

        /* Create Calculation and Calculate */
        $calculationId = ModflowId::generate();
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $scenarioId, $start, $end));
        $calculationList[] = [$calculationId, $ownerId, $scenarioId];

        /*
         * Begin add Scenario 2
         */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioId(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            $scenarioId,
            ModelName::fromString('Scenario 2'),
            ModelDescription::fromString('Simulation of MAR type injection wells'))
        );

        # THIS WELLS ARE THE YELLOW DOTS IN THE RIGHT IMAGE
        $header = array('name', 'x', 'y', 'srid', 'pumpingrate');
        $infiltrationWells = array(
            array('I_01', 585948, 2320333, 32648, 4000),
            array('I_02', 586348, 2319933, 32648, 4000),
            array('I_03', 586248, 2320033, 32648, 4000),
            array('I_04', 586148, 2320133, 32648, 4000),
            array('I_05', 586048, 2320233, 32648, 4000),
            array('I_06', 587648, 2322533, 32648, 4000),
            array('I_07', 587748, 2322533, 32648, 4000),
            array('I_08', 587848, 2322533, 32648, 4000),
            array('I_09', 587948, 2322533, 32648, 4000),
            array('I_10', 588048, 2322533, 32648, 4000)
        );
        foreach ($infiltrationWells as $row) {
            $wellData = array_combine($header, $row);
            $wellBoundary = WellBoundary::createWithParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                Geometry::fromPoint($geoTools->projectPoint(new Point($wellData['x'], $wellData['y'], $wellData['srid']), Srid::fromInt(4326))),
                WellType::fromString(WellType::TYPE_SCENARIO_NEW_WELL),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(1))
            );
            $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(
                    $start->toDateTimeImmutable(),
                    $wellData['pumpingrate']));
            $commandBus->dispatch(AddBoundary::to($scenarioId, $ownerId, $wellBoundary));
        }

        /* Calculation */
        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2007-12-31'));
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $scenarioId, $start, $end));
        $calculationList[] = [$calculationId, $ownerId, $scenarioId];

        /*
         * Begin add Scenario 3
         */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioId(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            $scenarioId,
            ModelName::fromString('Scenario 3'),
            ModelDescription::fromString('Combination of MAR types river bank filtration and injection wells'))
        );

        $boundariesFinder = $this->container->get('inowas.modflowmodel.boundaries_finder');
        $rbfRelocatedWellNamesAndGeometry = array(
            'H07_6' => $geoTools->projectPoint(new Point(588637, 2326840, 32648), Srid::fromInt(4326)),
            'H10_6' => $geoTools->projectPoint(new Point(589150, 2326214, 32648), Srid::fromInt(4326)),
            'H11_8' => $geoTools->projectPoint(new Point(593446, 2321044, 32648), Srid::fromInt(4326)),
            'H19_6' => $geoTools->projectPoint(new Point(589050, 2326431, 32648), Srid::fromInt(4326)),
            'H2_1'  => $geoTools->projectPoint(new Point(584451, 2331823, 32648), Srid::fromInt(4326)),
            'H2_8'  => $geoTools->projectPoint(new Point(593249, 2321333, 32648), Srid::fromInt(4326)),
            'H5_1'  => $geoTools->projectPoint(new Point(588440, 2327043, 32648), Srid::fromInt(4326)),
            'H8_6'  => $geoTools->projectPoint(new Point(588829, 2326631, 32648), Srid::fromInt(4326)),
            'H8_8'  => $geoTools->projectPoint(new Point(593443, 2321233, 32648), Srid::fromInt(4326)),
            'H9_1'  => $geoTools->projectPoint(new Point(584649, 2331729, 32648), Srid::fromInt(4326))
        );
        foreach ($rbfRelocatedWellNamesAndGeometry as $name => $geometry) {
            /** @var BoundaryId[] $boundaryIds */
            $boundaryIds = $boundariesFinder->getBoundaryIdsByName($scenarioId, BoundaryName::fromString($name));
            if (count($boundaryIds)==0){continue;}
            echo sprintf("Move Well %s.\r\n", $name);
            $boundaryId = $boundaryIds[0];
            $geometry = Geometry::fromPoint($geometry);
            $commandBus->dispatch(UpdateBoundaryGeometry::byUser($ownerId, $scenarioId, $boundaryId, $geometry));
        }

        $header = array('name', 'x', 'y', 'srid', 'pumpingrate');
        foreach ($infiltrationWells as $row) {
            $wellData = array_combine($header, $row);
            $wellBoundary = WellBoundary::createWithParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                Geometry::fromPoint($geoTools->projectPoint(new Point($wellData['x'], $wellData['y'], $wellData['srid']), Srid::fromInt(4326))),
                WellType::fromString(WellType::TYPE_SCENARIO_NEW_WELL),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(1))
            );
            $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams(
                $start->toDateTimeImmutable(),
                $wellData['pumpingrate']));
            $commandBus->dispatch(AddBoundary::to($scenarioId, $ownerId, $wellBoundary));
        }

        /* Add Head Results */
        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2007-12-31'));
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $scenarioId, $start, $end));
        $calculationList[] = [$calculationId, $ownerId, $scenarioId];

        foreach ($calculationList as $calculation) {
            $commandBus->dispatch(ChangeFlowPackage::byUserWithCalculationId($calculation[1], $calculation[0], PackageName::fromString('upw')));
            $commandBus->dispatch(UpdateCalculationPackageParameter::byUserWithModelId($calculationId, $ownerId, $modelId, 'upw', 'layTyp', Laytyp::fromInt(1)));

            $ocStressPeriodData = OcStressPeriodData::create()->addStressPeriod(OcStressPeriod::fromParams(0,0, ['save head', 'save drawdown']));
            $commandBus->dispatch(UpdateCalculationPackageParameter::byUserWithModelId($calculation[0], $calculation[1], $calculation[2], 'oc', 'ocStressPeriodData', $ocStressPeriodData));
            $commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($calculation[1], $calculation[0]));
        }
    }
}
