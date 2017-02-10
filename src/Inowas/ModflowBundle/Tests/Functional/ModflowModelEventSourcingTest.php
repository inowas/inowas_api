<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Tests\Functional;

use Inowas\Modflow\Model\Command\ChangeModflowModelArea;
use Inowas\Modflow\Model\Command\ChangeModflowModelBoundingBox;
use Inowas\Modflow\Model\Command\ChangeModflowModelDescription;
use Inowas\Modflow\Model\Command\ChangeModflowModelGridSize;
use Inowas\Modflow\Model\Command\ChangeModflowModelName;
use Inowas\Modflow\Model\Command\ChangeModflowModelSoilmodelId;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\ModflowModel;
use Inowas\Modflow\Model\ModflowModelActiveCells;
use Inowas\Modflow\Model\ModflowModelArea;
use Inowas\Modflow\Model\ModflowModelBoundingBox;
use Inowas\Modflow\Model\ModflowModelDescription;
use Inowas\Modflow\Model\ModflowModelGridSize;
use Inowas\Modflow\Model\ModflowModelId;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\Polygon;
use Inowas\Modflow\Model\SoilModelId;
use Prooph\ServiceBus\CommandBus;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ModflowModelEventSourcingTest extends KernelTestCase
{
    /** @var  CommandBus */
    protected $commandBus;

    /** @var ModflowModelList */
    protected $repository;

    public function setUp()
    {
        self::bootKernel();
        $this->commandBus = static::$kernel->getContainer()->get('prooph_service_bus.modflow_command_bus');
        $this->repository = static::$kernel->getContainer()->get('modflow_model_list');
    }

    public function testModflowModelCommands()
    {
        $modflowModelId = ModflowModelId::generate();
        $this->commandBus->dispatch(CreateModflowModel::withId($modflowModelId));
        $this->commandBus->dispatch(ChangeModflowModelName::forModflowModel($modflowModelId, ModflowModelName::fromString('MyNewModel')));
        $this->commandBus->dispatch(ChangeModflowModelDescription::forModflowModel($modflowModelId, ModflowModelDescription::fromString('MyNewModelDescription')));
        $this->commandBus->dispatch(ChangeModflowModelArea::forModflowModel(
            $modflowModelId,
            ModflowModelArea::fromPolygonAndActiveCells(
                Polygon::fromArray([[1,2], [2,2], [3,2], [4,2], [1,2]]),
                ModflowModelActiveCells::fromArray([[1,2], [2,2], [3,2], [4,2], [1,2]])
            )
        ));
        $this->commandBus->dispatch(ChangeModflowModelBoundingBox::forModflowModel($modflowModelId, ModflowModelBoundingBox::fromCoordinates(1,2,3,4,5)));
        $this->commandBus->dispatch(ChangeModflowModelGridSize::forModflowModel($modflowModelId, ModflowModelGridSize::fromXY(50, 60)));

        $soilmodelId = SoilModelId::generate();
        $this->commandBus->dispatch(ChangeModflowModelSoilmodelId::forModflowModel($modflowModelId, $soilmodelId));

        /** @var ModflowModel $model */
        $model = $this->repository->get($modflowModelId);
        $this->assertInstanceOf(ModflowModel::class, $model);
        $this->assertEquals($modflowModelId, $model->modflowModelId());
        $this->assertEquals(ModflowModelName::fromString('MyNewModel'), $model->name());
        $this->assertEquals(ModflowModelDescription::fromString('MyNewModelDescription'), $model->description());
        $this->assertEquals(
            ModflowModelArea::fromPolygonAndActiveCells(
                Polygon::fromArray([[1,2], [2,2], [3,2], [4,2], [1,2]]),
                ModflowModelActiveCells::fromArray([[1,2], [2,2], [3,2], [4,2], [1,2]])
            ), $model->area());
        $this->assertEquals(ModflowModelBoundingBox::fromCoordinates(1,2,3,4,5), $model->boundingBox());
        $this->assertEquals(ModflowModelGridSize::fromXY(50, 60), $model->gridSize());
        $this->assertEquals($soilmodelId, $model->soilmodelId());
    }
}
