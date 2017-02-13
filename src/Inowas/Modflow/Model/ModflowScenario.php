<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Modflow\Model\Event\ModflowScenarioWasCreated;

class ModflowScenario extends ModflowModel
{
    /** @var  ScenarioId */
    private $scenarioId;

    public static function createFromModflowModel(ScenarioId $scenarioId, ModflowModel $baseModel): ModflowScenario
    {
        $self = new self();
        $self->scenarioId = $scenarioId;
        $self->modflowModelId = $baseModel->modflowModelId();
        $self->name = ModflowModelName::fromString('Copy of '.$baseModel->name()->toString());
        $self->description = $baseModel->description();
        $self->boundaries = unserialize(serialize($baseModel->boundaries()));

        $self->recordThat(ModflowScenarioWasCreated::withId($baseModel->modflowModelId(), $scenarioId));
        return $self;
    }

    public function whenModflowScenarioWasCreated(ModflowScenarioWasCreated $event)
    {
        $this->scenarioId = $event->scenarioId();
        $this->modflowModelId = $event->modflowModelId();
    }

    public function scenarioId(): ScenarioId
    {
        return $this->scenarioId;
    }

    protected function aggregateId(): string
    {
        return $this->scenarioId->toString();
    }
}
