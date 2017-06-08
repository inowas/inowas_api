<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\Common\Boundaries\WellType;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Soilmodel\HydraulicAnisotropy;
use Inowas\Common\Soilmodel\HydraulicConductivityX;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\VerticalHydraulicConductivity;
use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
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
use Inowas\Common\Soilmodel\GeologicalLayer;
use Inowas\Common\Soilmodel\GeologicalLayerDescription;
use Inowas\Common\Soilmodel\GeologicalLayerId;
use Inowas\Common\Soilmodel\GeologicalLayerName;
use Inowas\Common\Soilmodel\GeologicalLayerNumber;
use Inowas\Common\Soilmodel\SoilmodelDescription;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\Common\Soilmodel\SoilmodelName;
use Inowas\Soilmodel\Model\Command\UpdateGeologicalLayerProperty;

class RioPrimero extends LoadScenarioBase
{

    public function load(): void
    {
        $this->loadUsers($this->container->get('fos_user.user_manager'));
        $geoTools = $this->container->get('inowas.geotools.geotools_service');
        $this->createEventStreamTableIfNotExists('event_stream');

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = UserId::fromString($this->ownerId);
        $modelId = ModflowId::generate();

        $area = Area::create(
            BoundaryId::generate(),
            BoundaryName::fromString('Rio Primero Area'),
            new Polygon(
                array(
                    array(
                        array(-63.687336, -31.313615),
                        array(-63.687336, -31.367449),
                        array(-63.569260, -31.367449),
                        array(-63.569260, -31.313615),
                        array(-63.687336, -31.313615)
                    )
                ), 4326
            ));

        $gridSize = GridSize::fromXY(75, 40);

        $commandBus->dispatch(CreateModflowModel::newWithIdAndUnits(
            $ownerId,
            $modelId,
            $area,
            $gridSize,
            TimeUnit::fromInt(TimeUnit::DAYS),
            LengthUnit::fromInt(LengthUnit::METERS)
        ));

        $box = $geoTools->projectBoundingBox(BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615, 4326), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $soilModelId = SoilmodelId::generate();
        $commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($ownerId, $modelId, $soilModelId));
        $commandBus->dispatch(CreateSoilmodel::byUserWithModelId($ownerId, $soilModelId));
        $commandBus->dispatch(ChangeSoilmodelName::forSoilmodel($ownerId, $soilModelId, SoilmodelName::fromString('SoilModel Río Primero')));
        $commandBus->dispatch(ChangeSoilmodelDescription::forSoilmodel($ownerId, $soilModelId, SoilmodelDescription::fromString('SoilModel for Río Primero Area')));

        $layers = [['Surface Layer', 'the one and only']];
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

            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, TopElevation::fromLayerValue(430)));
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, BottomElevation::fromLayerValue(360)));
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, HydraulicConductivityX::fromLayerValue(10)));
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, HydraulicAnisotropy::fromLayerValue(1)));
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, VerticalHydraulicConductivity::fromLayerValue(1)));
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, SpecificStorage::fromLayerValue(1e-5)));
            $commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, SpecificYield::fromLayerValue(0.15)));
        }

        /*
        $boreHoles = array(
            array('point', 'name', 'top', 'bot'),
            array(new Point(-63.64698, -31.32741, 4326), 'GP1', 465, 392),
            array(new Point(-63.64630, -31.34237, 4326), 'GP2', 460, 390),
            array(new Point(-63.64544, -31.35967, 4326), 'GP3', 467, 395),
            array(new Point(-63.61591, -31.32404, 4326), 'GP4', 463, 392),
            array(new Point(-63.61420, -31.34383, 4326), 'GP5', 463, 394),
            array(new Point(-63.61506, -31.36011, 4326), 'GP6', 465, 392),
            array(new Point(-63.58536, -31.32653, 4326), 'GP7', 465, 393),
            array(new Point(-63.58261, -31.34266, 4326), 'GP8', 460, 392),
            array(new Point(-63.58459, -31.35573, 4326), 'GP9', 460, 390)
        );

        $header = null;
        foreach ($boreHoles as $borehole) {
            if (null === $header) {
                $header = $borehole;
                continue;
            }

            $borehole = array_combine($header, $borehole);
            echo sprintf("Add BoreHole %s to soilmodel %s.\r\n", $borehole['name'], $soilModelId->toString());

            $boreLogId = BoreLogId::generate();
            $boreLogName = BoreLogName::fromString($borehole['name']);
            $boreLogLocation = BoreLogLocation::fromPoint($borehole['point']);
            $commandBus->dispatch(CreateBoreLog::byUser($ownerId, $boreLogId, $boreLogName, $boreLogLocation));
            $commandBus->dispatch(AddBoreLogToSoilmodel::byUserWithId($ownerId, $soilModelId, $boreLogId));

            $horizon = Horizon::fromParams(
                HorizonId::generate(),
                GeologicalLayerNumber::fromInteger(0),
                HTop::fromMeters($borehole['top']),
                HBottom::fromMeters($borehole['bot']),
                Conductivity::fromXYZinMPerDay(
                    HydraulicConductivityX::fromPointValue(10),
                    HydraulicConductivityY::fromPointValue(10),
                    HydraulicConductivityZ::fromPointValue(1)
                ),
                Storage::fromParams(
                    SpecificStorage::fromPointValue(1e-5),
                    SpecificYield::fromPointValue(0.2)
                )
            );
            $commandBus->dispatch(AddHorizonToBoreLog::byUserWithId($ownerId, $boreLogId, $horizon));
        }

        echo sprintf("Interpolate soilmodel with %s Memory usage\r\n", memory_get_usage());
        $commandBus->dispatch(InterpolateSoilmodel::forSoilmodel($ownerId, $soilModelId, $boundingBox, $gridSize));
        */


        /*
         * Add Wells for the BaseScenario
         */
        $wells = array(
            array('name', 'point', 'type', 'layer', 'date', 'pumpingRate'),
            array('Irrigation Well 1', new Point(-63.671125, -31.325009, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 2', new Point(-63.659952, -31.330144, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 3', new Point(-63.674691, -31.342506, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 4', new Point(-63.637379, -31.359613, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 5', new Point(-63.582069, -31.324063, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Public Well 1', new Point(-63.625402, -31.329897, 4326), WellType::TYPE_PUBLIC_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Public Well 2', new Point(-63.623027, -31.331184, 4326), WellType::TYPE_PUBLIC_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
        );

        $header = null;
        foreach ($wells as $data){
            if (null === $header){
                $header = $data;
                continue;
            }

            $data = array_combine($header, $data);
            $wellBoundary = WellBoundary::createWithParams(
                BoundaryId::generate(),
                BoundaryName::fromString($data['name']),
                Geometry::fromPoint($data['point']),
                WellType::fromString($data['type']),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger($data['layer']))

            );

            echo sprintf("Add well with name %s.\r\n", $data['name']);
            $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams($data['date'], $data['pumpingRate']));
            $commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $wellBoundary));
        }

        /* Create calculation and calculate */
        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2005-12-31'));
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modelId, $start, $end));
        $commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($ownerId, $calculationId));

        /*
         * Create ScenarioAnalysis from BaseModel
         */
        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $commandBus->dispatch(CreateScenarioAnalysis::byUserWithBaseModelNameAndDescription(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('ScenarioAnalysis: Rio Primero 2020'),
            ScenarioAnalysisDescription::fromString('ScenarioAnalysis: Rio Primero 2020')
        ));

        /*
         * Begin add Scenario 1
         */
        $scenarioId = ModflowId::generate();
        $commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioId(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            $scenarioId,
            ModelName::fromString('Scenario 0: Rio Primero 2020'),
            ModelDescription::fromString('Future Prediction for the year 2020'))
        );

        $wells = array(
            array('name', 'point', 'type', 'layer', 'date', 'pumpingRate'),
            array('Irrigation Well 6', new Point(-63.65101, -31.33516, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 7', new Point(-63.64792, -31.33546, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 8', new Point(-63.66714, -31.34513, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 9', new Point(-63.6644, -31.34513, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 10', new Point(-63.60363, -31.32578, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 11', new Point(-63.59367, -31.35803, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 12', new Point(-63.60123, -31.32578, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Irrigation Well 13', new Point(-63.58852, -31.35803, 4326), WellType::TYPE_INDUSTRIAL_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Public Well 3', new Point(-63.62383, -31.34, 4326), WellType::TYPE_PUBLIC_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
            array('Public Well 4', new Point(-63.6216, -31.34162, 4326), WellType::TYPE_PUBLIC_WELL, 0, new \DateTimeImmutable('2015-01-01'), -5000),
        );

        $header = null;
        foreach ($wells as $data){
            if (null === $header){
                $header = $data;
                continue;
            }

            $data = array_combine($header, $data);
            $wellBoundary = WellBoundary::createWithParams(
                BoundaryId::generate(),
                BoundaryName::fromString($data['name']),
                Geometry::fromPoint($data['point']),
                WellType::fromString($data['type']),
                AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger($data['layer']))

            );

            echo sprintf("Add well with name %s.\r\n", $data['name']);
            $wellBoundary = $wellBoundary->addPumpingRate(WellDateTimeValue::fromParams($data['date'], $data['pumpingRate']));
            $commandBus->dispatch(AddBoundary::to($scenarioId, $ownerId, $wellBoundary));
        }

        /* Create calculation and calculate */
        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2005-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2005-12-31'));
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $scenarioId, $start, $end));
        $commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($ownerId, $calculationId));
    }
}
