<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
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
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Grid\BoundingBox;
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
use Inowas\ModflowModel\Model\Command\ChangeBoundingBox;
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

class RioPrimeroArea extends LoadScenarioBase
{

    public function load(): void
    {
        $this->loadUsers($this->container->get('fos_user.user_manager'));

        $commandBus = $this->container->get('prooph_service_bus.modflow_command_bus');
        $ownerId = UserId::fromString($this->ownerId);

        $baseModelId = ModflowId::generate();
        $polygon = new Polygon([[
            [-63.687336, -31.313615],
            [-63.687336, -31.367449],
            [-63.569260, -31.367449],
            [-63.569260, -31.313615],
            [-63.687336, -31.313615]
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
            LengthUnit::fromInt(LengthUnit::METERS)
        ));
    }
}
