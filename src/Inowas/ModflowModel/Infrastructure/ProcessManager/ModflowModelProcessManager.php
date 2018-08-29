<?php

namespace Inowas\ModflowModel\Infrastructure\ProcessManager;

use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Status\Visibility;
use Inowas\ModflowModel\Model\Event\DescriptionWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\Tool\Model\Command\CreateToolInstance;
use Inowas\Tool\Model\Command\UpdateToolInstance;
use Inowas\Tool\Model\ToolData;
use Inowas\Tool\Model\ToolId;
use Inowas\Tool\Model\ToolType;
use Prooph\Common\Messaging\DomainEvent;
use Prooph\ServiceBus\CommandBus;

class ModflowModelProcessManager
{
    /** @var  CommandBus */
    private $commandBus;

    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $toolId = ToolId::fromString($event->modelId()->toString());
        $this->commandBus->dispatch(CreateToolInstance::newWithAllParams(
            $event->userId(),
            $toolId,
            ToolType::fromString(ToolType::MODEL_SETUP),
            Name::fromString(''),
            Description::fromString(''),
            ToolData::create(),
            Visibility::fromBool(false)
        ));
    }

    public function onModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        if (!$event->isTool()) {
            return;
        }

        // Todo

    }

    public function onNameWasChanged(NameWasChanged $event): void
    {
        $toolId = ToolId::fromString($event->modelId()->toString());
        $this->commandBus->dispatch(UpdateToolInstance::newWithAllParams(
            $event->userId(),
            $toolId,
            $event->name()
        ));
    }

    public function onDescriptionWasChanged(DescriptionWasChanged $event): void
    {
        $toolId = ToolId::fromString($event->modelId()->toString());
        $this->commandBus->dispatch(UpdateToolInstance::newWithAllParams(
            $event->userId(),
            $toolId,
            null,
            $event->description()
        ));
    }

    public function onEvent(DomainEvent $e): void
    {
        $handler = $this->determineEventMethodFor($e);
        if (! method_exists($this, $handler)) {
            throw new \RuntimeException(sprintf(
                'Missing event method %s for projector %s',
                $handler,
                \get_class($this)
            ));
        }
        $this->{$handler}($e);
    }

    protected function determineEventMethodFor(DomainEvent $e): string
    {
        return 'on' . implode(\array_slice(explode('\\', \get_class($e)), -1));
    }
}
