<?php

namespace Inowas\ModflowBundle\DataFixtures\Scenarios\RioPrimero;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\ModflowId;
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
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\Sy;
use Inowas\Common\Modflow\Top;
use Inowas\Common\Modflow\Vka;
use Inowas\Common\Soilmodel\Layer;
use Inowas\Common\Soilmodel\LayerId;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowModel\Model\Command\AddLayer;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowBundle\DataFixtures\Scenarios\LoadScenarioBase;
use Inowas\ModflowModel\Model\Command\UpdateStressPeriods;
use Inowas\Soilmodel\Model\LayerInterpolationConfiguration;

class RioPrimeroBaseModelSanDiego extends LoadScenarioBase
{
    /**
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

        $start = DateTime::fromString('2015-01-01');
        $end = DateTime::fromString('2025-12-31');
        $stressperiods = StressPeriods::create($start, $end, TimeUnit::fromInt(TimeUnit::DAYS));
        $stressperiods->addStressPeriod(StressPeriod::create(0, 365, 1, 1, true));

        $commandBus->dispatch(UpdateStressPeriods::of($ownerId, $baseModelId, $stressperiods));
    }
}
