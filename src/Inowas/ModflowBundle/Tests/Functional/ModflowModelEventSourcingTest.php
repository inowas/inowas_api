<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Tests\Functional;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\GeoToolsBundle\Service\GeoTools;
use Inowas\Modflow\Model\AreaBoundary;
use Inowas\Modflow\Model\BoundaryGeometry;
use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\BoundaryName;
use Inowas\Modflow\Model\Command\AddModflowModelBoundary;
use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Command\ChangeModflowModelDescription;
use Inowas\Modflow\Model\Command\ChangeModflowModelGridSize;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Command\CreateModflowScenario;
use Inowas\Modflow\Model\Command\RemoveModflowModelBoundary;
use Inowas\Modflow\Model\LayerNumber;
use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowModel;
use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelGridSize;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\ModflowScenario;
use Inowas\Modflow\Model\PumpingRate;
use Inowas\Modflow\Model\ScenarioId;
use Inowas\Modflow\Model\SoilModelId;
use Inowas\Modflow\Model\UserId;
use Inowas\Modflow\Model\WellBoundary;
use Inowas\Modflow\Model\WellType;
use Inowas\ModflowBundle\Model\BoundingBox;
use Prooph\EventStore\EventStore;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowModelEventSourcingTest extends KernelTestCase
{
    /** @var  CommandBus */
    protected $commandBus;

    /** @var  EventStore */
    protected $eventStore;

    /** @var  GeoTools */
    protected $geoTools;

    /** @var ModflowModelList */
    protected $modelRepository;

    public function setUp()
    {
        self::bootKernel();
        $this->commandBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->eventStore = static::$kernel->getContainer()->get('prooph_event_store.modflow_model_store');
        $this->modelRepository = static::$kernel->getContainer()->get('modflow_model_list');
        $this->geoTools = static::$kernel->getContainer()->get('inowas.geotools.geotools');
    }

    public function testModflowModelCommands()
    {
        $ownerId = UserId::generate();
        $modflowModelId = ModflowModelId::generate();
        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modflowModelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modflowModelId, ModflowModelName::fromString('MyNewModel')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($ownerId, $modflowModelId, ModflowModelDescription::fromString('MyNewModelDescription')));

        $areaId = BoundaryId::generate();
        $area = AreaBoundary::create($areaId);
        $this->commandBus->dispatch(AddModflowModelBoundary::forModflowModel($modflowModelId, $area));
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modflowModelId, ModflowModelBoundingBox::fromCoordinates(1, 2, 3, 4, 5)));
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modflowModelId, ModflowModelGridSize::fromXY(50, 60)));

        $soilmodelId = SoilModelId::generate();
        $this->commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($modflowModelId, $soilmodelId));

        /** @var ModflowModel $model */
        $model = $this->modelRepository->get($modflowModelId);
        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals($ownerId, $model->ownerId());
        $this->assertEquals($modflowModelId, $model->modflowModelId());
        $this->assertEquals(ModflowModelName::fromString('MyNewModel'), $model->name());
        $this->assertEquals(ModflowModelDescription::fromString('MyNewModelDescription'), $model->description());
        $this->assertEquals($areaId, $model->area()->boundaryId());
        $this->assertEquals(ModflowModelBoundingBox::fromCoordinates(1, 2, 3, 4, 5), $model->boundingBox());
        $this->assertEquals(ModflowModelGridSize::fromXY(50, 60), $model->gridSize());
        $this->assertEquals($soilmodelId, $model->soilmodelId());

        $wellId = BoundaryId::generate();
        $well = WellBoundary::create($wellId);
        $this->commandBus->dispatch(AddModflowModelBoundary::forModflowModel($modflowModelId, $well));
        $model = $this->modelRepository->get($modflowModelId);
        $this->assertCount(1, $model->boundaries());

        $this->commandBus->dispatch(RemoveModflowModelBoundary::forModflowModel($modflowModelId, $wellId));
        $model = $this->modelRepository->get($modflowModelId);
        $this->assertCount(0, $model->boundaries());

        $scenarioId = ScenarioId::generate();
        $this->commandBus->dispatch(CreateModflowScenario::byUserWithIds($ownerId, $scenarioId, $modflowModelId));
        $model = $this->modelRepository->get($modflowModelId);
        $this->assertTrue(is_array($model->scenarios()));
        $this->assertCount(1, $model->scenarios());
        $this->assertInstanceOf(ModflowScenario::class, $model->scenarios()[$scenarioId->toString()]);

        /** @var  ModflowScenario $scenario * */
        $scenario = $model->scenarios()[$scenarioId->toString()];
        $this->assertEquals('Copy of MyNewModel', $scenario->name()->toString());

        $wellId = BoundaryId::generate();
        $well = WellBoundary::create($wellId);
        $this->commandBus->dispatch(AddModflowModelBoundary::forModflowScenario($modflowModelId, $scenarioId, $well));
        $model = $this->modelRepository->get($modflowModelId);
        $scenario = $model->scenarios()[$scenarioId->toString()];
        $this->assertCount(1, $scenario->boundaries());

        /** @var ModflowBoundary $well */
        $well = $scenario->boundaries()[$wellId->toString()];
        $this->assertInstanceOf(WellBoundary::class, $well);
        $this->assertEquals($wellId, $well->boundaryId());

        $this->commandBus->dispatch(RemoveModflowModelBoundary::forModflowScenario($modflowModelId, $scenarioId, $well->boundaryId()));
        $model = $this->modelRepository->get($modflowModelId);
        $scenario = $model->scenarios()[$scenarioId->toString()];
        $this->assertCount(0, $scenario->boundaries());
    }


    public function testModflowModelCommandsAgain()
    {
        $ownerId = UserId::generate();
        $modelId = ModflowModelId::generate();

        $this->commandBus->dispatch(CreateModflowModel::byUserWithModelId($ownerId, $modelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($ownerId, $modelId, ModflowModelName::fromString('BaseModel INOWAS Hanoi')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel(
            $ownerId,
            $modelId,
            ModflowModelDescription::fromString(
                'Application of managed aquifer recharge for maximization of water storage capacity in Hanoi.'
            )
        ));

        $area = AreaBoundary::create(BoundaryId::generate())
            ->setName(BoundaryName::fromString('Hanoi Area'))
            ->setGeometry(BoundaryGeometry::fromPolygon(new Polygon(array(
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
        ), 4326)));

        $this->commandBus->dispatch(AddModflowModelBoundary::forModflowModel($modelId, $area));

        $box = $this->geoTools->transformBoundingBox(new BoundingBox(578205, 594692, 2316000, 2333500, 32648), 4326);
        $boundingBox = ModflowModelBoundingBox::fromEPSG4326Coordinates(
            $box->getXMin(),
            $box->getXMax(),
            $box->getYMin(),
            $box->getYMax()
        );

        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($ownerId, $modelId, $boundingBox));
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($ownerId, $modelId, ModflowModelGridSize::fromXY(165, 175)));

        $wells = [[23, 'LN11', 11788984.59457647800445557, 2389010.63655604887753725, -40, -70, 4320, -2135, 11788984.59, 2389010.64]];

        $header = array('id', 'name', 'wkt_x', 'wkt_y', 'ztop', 'zbot', 'stoptime', 'pumpingrate', 'x', 'y');
        foreach ($wells as $row) {
            $wellData = array_combine($header, $row);
            $well = WellBoundary::createWithAllParams(
                BoundaryId::generate(),
                BoundaryName::fromString($wellData['name']),
                BoundaryGeometry::fromPoint($this->geoTools->transformPoint(new Point($wellData['x'], $wellData['y'], 3857), 4326)),
                WellType::fromString(WellType::TYPE_PUBLIC_WELL),
                LayerNumber::fromInteger(4),
                PumpingRate::fromValue($wellData['pumpingrate'])
            );

            $this->commandBus->dispatch(AddModflowModelBoundary::forModflowModel($modelId, $well));
        }

        /** @var ModflowModel $model */
        $model = $this->modelRepository->get($modelId);
        $this->assertInstanceOf(AreaBoundary::class, $model->area());
        $this->assertInstanceOf(BoundaryId::class, $model->area()->boundaryId());
        $this->assertEquals($area->boundaryId(), $model->area()->boundaryId());
        $this->assertInstanceOf(BoundaryName::class, $model->area()->name());
        $this->assertEquals('Hanoi Area', $model->area()->name()->toString());
        $this->assertInstanceOf(BoundaryGeometry::class, $model->area()->geometry());
        $this->assertInstanceOf(ModflowModelBoundingBox::class, $model->boundingBox());
        $this->assertEquals($boundingBox, $model->boundingBox());
        $this->assertInstanceOf(ModflowModelGridSize::class, $model->gridSize());
        $this->assertEquals(ModflowModelGridSize::fromXY(165, 175), $model->gridSize());
        $this->assertCount(1, $model->boundaries());

        /** @var WellBoundary $well */
        $well = array_values($model->boundaries())[0];
        $this->assertInstanceOf(BoundaryId::class, $well->boundaryId());
        $this->assertEquals('LN11', $well->name()->toString());
        $this->assertEquals('{"type":"Point","coordinates":[105.90225041447,20.975946029725]}', $well->geometry()->toJson());
        $this->assertEquals('puw', $well->wellType()->type());
        $this->assertEquals(4, $well->layerNumber()->toInteger());
        $this->assertEquals(-2135, $well->pumpingRate()->toFloat());

        $scenarioId = ScenarioId::generate();
        $this->commandBus->dispatch(CreateModflowScenario::byUserWithIds($ownerId, $scenarioId, $modelId));
        $model = $this->modelRepository->get($modelId);
        $this->assertCount(1, $model->scenarios());

        /** @var ModflowScenario $scenario */
        $scenario = array_values($model->scenarios())[0];
        $this->assertEquals($scenarioId, $scenario->scenarioId());
        $this->assertEquals($ownerId, $scenario->ownerId());

        $well = array_values($scenario->boundaries())[0];
        $this->assertInstanceOf(BoundaryId::class, $well->boundaryId());
        $this->assertEquals('LN11', $well->name()->toString());
        $this->assertEquals('{"type":"Point","coordinates":[105.90225041447,20.975946029725]}', $well->geometry()->toJson());
        $this->assertEquals('puw', $well->wellType()->type());
        $this->assertEquals(4, $well->layerNumber()->toInteger());
        $this->assertEquals(-2135, $well->pumpingRate()->toFloat());
    }
}
