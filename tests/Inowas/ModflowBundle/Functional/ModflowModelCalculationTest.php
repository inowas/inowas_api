<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\ObservationPointName;
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
use Inowas\Common\Geometry\Srid;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\Laytyp;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\ModelName;
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
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\UpdateCalculationStressperiods;
use Inowas\ModflowModel\Model\Command\AddBoundary;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\ModflowModel\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\Command\FinishEditingBoundaries;
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
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class ModflowModelCalculationTest extends EventSourcingBaseTest
{
    public function test_create_rio_primero_model(): void
    {
        $geoTools = $this->container->get('inowas.geotools.geotools_service');
        
        $ownerId = UserId::generate();
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

        $this->commandBus->dispatch(CreateModflowModel::newWithIdAndUnits(
            $ownerId,
            $modelId,
            $area,
            $gridSize,
            TimeUnit::fromInt(TimeUnit::DAYS),
            LengthUnit::fromInt(LengthUnit::METERS)
        ));

        $box = $geoTools->projectBoundingBox(BoundingBox::fromCoordinates(-63.687336, -63.569260, -31.367449, -31.313615, 4326), Srid::fromInt(4326));
        $boundingBox = BoundingBox::fromEPSG4326Coordinates($box->xMin(), $box->xMax(), $box->yMin(), $box->yMax(), $box->dX(), $box->dY());
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));

        $soilModelId = SoilmodelId::generate();
        $this->commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($ownerId, $modelId, $soilModelId));
        $this->commandBus->dispatch(CreateSoilmodel::byUserWithModelId($ownerId, $soilModelId));
        $this->commandBus->dispatch(ChangeSoilmodelName::forSoilmodel($ownerId, $soilModelId, SoilmodelName::fromString('SoilModel Río Primero')));
        $this->commandBus->dispatch(ChangeSoilmodelDescription::forSoilmodel($ownerId, $soilModelId, SoilmodelDescription::fromString('SoilModel for Río Primero Area')));

        $layers = [['Surface Layer', 'the one and only']];
        foreach ($layers as $key => $layer) {
            $layerId = GeologicalLayerId::generate();
            $type = Laytyp::fromValue(Laytyp::TYPE_CONVERTIBLE);
            $layerNumber = GeologicalLayerNumber::fromInteger($key);

            $this->commandBus->dispatch(
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

            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, TopElevation::fromLayerValue(430)));
            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, BottomElevation::fromLayerValue(360)));
            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, HydraulicConductivityX::fromLayerValue(10)));
            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, HydraulicAnisotropy::fromLayerValue(1)));
            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, VerticalHydraulicConductivity::fromLayerValue(1)));
            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, SpecificStorage::fromLayerValue(1e-5)));
            $this->commandBus->dispatch(UpdateGeologicalLayerProperty::forSoilmodel($ownerId, $soilModelId, $layerId, SpecificYield::fromLayerValue(0.2)));
        }

        /*
         * Add GeneralHeadBoundaries
         * GHB1
         */
        $ghb = GeneralHeadBoundary::createWithParams(
            BoundaryId::generate(),
            BoundaryName::fromString('General Head Boundary 1'),
            Geometry::fromLineString(new LineString(array(
                array($boundingBox->xMin(), $boundingBox->yMin()),
                array($boundingBox->xMin(), $boundingBox->yMax())
            ), $boundingBox->srid())),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $observationPointId = ObservationPointId::generate();
        $observationPoint = ObservationPoint::fromIdNameAndGeometry(
            $observationPointId,
            ObservationPointName::fromString('OP 1'),
            Geometry::fromPoint(new Point($boundingBox->xMax(), $boundingBox->yMin(), 4326))
        );

        $ghb->addObservationPoint($observationPoint);
        $ghb->addGeneralHeadValueToObservationPoint(
            $observationPointId,
            GeneralHeadDateTimeValue::fromParams(
                new \DateTimeImmutable('2015-01-01'),
                450,
                100
            )
        );

        $this->commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $ghb));

        /*
         * GHB2
         */
        $ghb = GeneralHeadBoundary::createWithParams(
            BoundaryId::generate(),
            BoundaryName::fromString('General Head Boundary 2'),
            Geometry::fromLineString(new LineString(array(
                array($boundingBox->xMax(), $boundingBox->yMin()),
                array($boundingBox->xMax(), $boundingBox->yMax())
            ), $boundingBox->srid())),
            AffectedLayers::createWithLayerNumber(LayerNumber::fromInteger(0))
        );

        $observationPointId = ObservationPointId::generate();
        $observationPoint = ObservationPoint::fromIdNameAndGeometry(
            $observationPointId,
            ObservationPointName::fromString('OP 1'),
            Geometry::fromPoint(new Point($boundingBox->xMax(), $boundingBox->yMin(), 4326))
        );

        $ghb->addObservationPoint($observationPoint);
        $ghb->addGeneralHeadValueToObservationPoint(
            $observationPointId,
            GeneralHeadDateTimeValue::fromParams(
                new \DateTimeImmutable('2015-01-01'),
                440,
                100
            )
        );

        $this->commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $ghb));

        /*
         * Add RiverBoundary
         * RIV
         */
        $riv = RiverBoundary::createWithParams(
            BoundaryId::generate(),
            BoundaryName::fromString('Rio Primero River'),
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
        $observationPoint = ObservationPoint::fromIdNameAndGeometry(
            $observationPointId,
            ObservationPointName::fromString('OP 1'),
            Geometry::fromPoint(new Point(-63.673968315125,-31.366206539217, 4326))
        );

        $riv->addObservationPoint($observationPoint);
        $riv->addRiverStageToObservationPoint(
            $observationPointId,
            RiverDateTimeValue::fromParams(
                new \DateTimeImmutable('2015-01-01'),
                446,
                444,
                200
            )
        );

        $this->commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $riv));

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
            $this->commandBus->dispatch(AddBoundary::to($modelId, $ownerId, $wellBoundary));
        }

        /* Create calculation and calculate */
        $calculationId = ModflowId::generate();
        $start = DateTime::fromDateTime(new \DateTime('2015-01-01'));
        $end = DateTime::fromDateTime(new \DateTime('2015-12-31'));
        $this->commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $ownerId, $modelId, $start, $end));

        $stressperiods = StressPeriods::create($start, $end, TimeUnit::fromInt(TimeUnit::DAYS));
        $stressperiods->addStressPeriod(StressPeriod::create(0, 1,1,1,true));
        $stressperiods->addStressPeriod(StressPeriod::create(1, 365,365,1,false));
        $this->commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($ownerId, $calculationId, $stressperiods));

        $config = $this->container->get('inowas.modflowcalculation.calculation_configuration_finder')->getConfigurationJson($calculationId);
        dump($config);
        #$this->commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($ownerId, $calculationId));

        /*
         * Create ScenarioAnalysis from BaseModel
         */
        $scenarioAnalysisId = ScenarioAnalysisId::generate();
        $this->commandBus->dispatch(CreateScenarioAnalysis::byUserWithBaseModelNameAndDescription(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            ScenarioAnalysisName::fromString('ScenarioAnalysis: Rio Primero 2020'),
            ScenarioAnalysisDescription::fromString('ScenarioAnalysis: Rio Primero 2020')
        ));

        /*
         * Begin add Scenario 0
         */
        $scenarioId = ModflowId::generate();
        $this->commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioId(
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
            $this->commandBus->dispatch(AddBoundary::to($scenarioId, $ownerId, $wellBoundary));
        }

        $this->commandBus->dispatch(FinishEditingBoundaries::to($scenarioId, $ownerId));
        $configuration = $this->container->get('inowas.modflowcalculation.calculation_configuration_finder')->getConfigurationByModelId($scenarioId);
        dump(json_encode($configuration));

        /*
         * Begin add Scenario 1
         */
        $scenarioId = ModflowId::generate();
        $this->commandBus->dispatch(CreateScenario::byUserWithBaseModelAndScenarioId(
            $scenarioAnalysisId,
            $ownerId,
            $modelId,
            $scenarioId,
            ModelName::fromString('Scenario 1: River bank filtration'),
            ModelDescription::fromString('Move the wells next to the river'))
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
            $this->commandBus->dispatch(AddBoundary::to($scenarioId, $ownerId, $wellBoundary));
        }

        $this->commandBus->dispatch(FinishEditingBoundaries::to($scenarioId, $ownerId));
        $configuration = $this->container->get('inowas.modflowcalculation.calculation_configuration_finder')->getConfigurationByModelId($scenarioId);
        dump(json_encode($configuration));
    }
}
