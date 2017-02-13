<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Tests\Functional;

use Inowas\Modflow\Model\AreaBoundary;
use Inowas\Modflow\Model\BoundaryId;
use Inowas\Modflow\Model\Command\AddModflowModelBoundary;
use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Command\ChangeModflowModelDescription;
use Inowas\Modflow\Model\Command\ChangeModflowModelGridSize;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Command\CreateModflowScenario;
use Inowas\Modflow\Model\Command\RemoveModflowModelBoundary;
use Inowas\Modflow\Model\ModflowBoundary;
use Inowas\Modflow\Model\ModflowModel;
use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelGridSize;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\ModflowScenario;
use Inowas\Modflow\Model\ModflowScenarioList;
use Inowas\Modflow\Model\ScenarioId;
use Inowas\Modflow\Model\SoilModelId;
use Inowas\Modflow\Model\WellBoundary;
use Prooph\EventStore\EventStore;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowModelEventSourcingTest extends KernelTestCase
{
    /** @var  CommandBus */
    protected $commandBus;

    /** @var  EventStore */
    protected $eventStore;

    /** @var ModflowModelList */
    protected $modelRepository;

    /** @var ModflowScenarioList */
    protected $scenarioRepository;

    public function setUp()
    {
        self::bootKernel();
        $this->commandBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->eventStore = static::$kernel->getContainer()->get('prooph_event_store.modflow_model_store');
        $this->modelRepository = static::$kernel->getContainer()->get('modflow_model_list');
    }

    public function testModflowModelCommands()
    {
        $modflowModelId = ModflowModelId::generate();
        $this->commandBus->dispatch(CreateModflowModel::withId($modflowModelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($modflowModelId, ModflowModelName::fromString('MyNewModel')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($modflowModelId, ModflowModelDescription::fromString('MyNewModelDescription')));

        $areaId = BoundaryId::generate();
        $area = AreaBoundary::create($areaId);
        $this->commandBus->dispatch(AddModflowModelBoundary::forModflowModel($modflowModelId, $area));
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($modflowModelId, ModflowModelBoundingBox::fromCoordinates(1,2,3,4,5)));
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($modflowModelId, ModflowModelGridSize::fromXY(50, 60)));

        $soilmodelId = SoilModelId::generate();
        $this->commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($modflowModelId, $soilmodelId));

        /** @var ModflowModel $model */
        $model = $this->modelRepository->get($modflowModelId);
        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals($modflowModelId, $model->modflowModelId());
        $this->assertEquals(ModflowModelName::fromString('MyNewModel'), $model->name());
        $this->assertEquals(ModflowModelDescription::fromString('MyNewModelDescription'), $model->description());
        $this->assertEquals($areaId, $model->area()->boundaryId());
        $this->assertEquals(ModflowModelBoundingBox::fromCoordinates(1,2,3,4,5), $model->boundingBox());
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
        $this->commandBus->dispatch(CreateModflowScenario::withId($scenarioId, $modflowModelId));
        $model = $this->modelRepository->get($modflowModelId);
        $this->assertTrue(is_array($model->scenarios()));
        $this->assertCount(1, $model->scenarios());
        $this->assertInstanceOf(ModflowScenario::class, $model->scenarios()[$scenarioId->toString()]);

        /** @var  ModflowScenario $scenario **/
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
}
