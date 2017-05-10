<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\ModflowModel\Model\Event\ScenarioAnalysisWasCreated;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisDescriptionWasChanged;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioAnalysisNameWasChanged;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasAdded;
use Inowas\ScenarioAnalysis\Model\Event\ScenarioWasRemoved;
use Prooph\EventSourcing\AggregateRoot;

class ScenarioAnalysisAggregate extends AggregateRoot
{

    /** @var  ScenarioAnalysisId */
    protected $id;

    /** @var  ModflowId */
    protected $baseModelId;

    /** @var  UserId */
    protected $ownerId;

    /** @var  bool */
    protected $public;

    /** @var ScenarioAnalysisName */
    protected $name;

    /** @var ScenarioAnalysisDescription */
    protected $description;

    /** @var array */
    protected $scenarios;

    public static function create(ScenarioAnalysisId $scenarioAnalysisId, UserId $userId, ModflowId $modflowId): ScenarioAnalysisAggregate
    {
        $self = new self();
        $self->baseModelId = $modflowId;
        $self->ownerId = $userId;
        $self->scenarios = [];

        $self->recordThat(ScenarioAnalysisWasCreated::byUserWithId($scenarioAnalysisId, $userId, $modflowId));
        return $self;
    }

    public function addScenario(UserId $userId, ModflowId $scenarioId): void
    {
        if (in_array($scenarioId->toString(), $this->scenarios)){
            return;
        }

        $this->scenarios[] = $scenarioId->toString();
        $this->recordThat(ScenarioWasAdded::to($this->id, $userId, $scenarioId));
    }

    public function removeScenario(UserId $userId, ModflowId $scenarioId): void
    {
        if (! in_array($scenarioId->toString(), $this->scenarios)){
            return;
        }

        $this->scenarios = array_diff($this->scenarios, [$scenarioId->toString()]);
        $this->recordThat(ScenarioWasRemoved::from($this->id, $userId, $scenarioId));
    }

    public function changeName(UserId $userId, ScenarioAnalysisName $name): void
    {
        $this->name = $name;
        $this->recordThat(ScenarioAnalysisNameWasChanged::of($this->id, $userId, $name));
    }

    public function changeDescription(UserId $userId, ScenarioAnalysisDescription $description): void
    {
        $this->description = $description;
        $this->recordThat(ScenarioAnalysisDescriptionWasChanged::of($this->id, $userId, $description));
    }

    public function scenarioAnalysisId(): ScenarioAnalysisId
    {
        return $this->id;
    }

    public function baseModelId(): ModflowId
    {
        return $this->baseModelId;
    }

    public function ownerId(): UserId
    {
        return $this->ownerId;
    }

    public function isPublic(): bool
    {
        return $this->public;
    }

    public function name(): ScenarioAnalysisName
    {
        return $this->name;
    }

    public function description(): ScenarioAnalysisDescription
    {
        return $this->description;
    }

    public function scenarios(): array
    {
        return $this->scenarios;
    }

    protected function whenScenarioAnalysisWasCreated(ScenarioAnalysisWasCreated $event): void
    {
        $this->id = $event->scenarioAnalysisId();
        $this->baseModelId = $event->baseModelId();
        $this->ownerId = $event->userId();
        $this->scenarios = [];
    }

    protected function whenScenarioWasAdded(ScenarioWasAdded $event): void
    {
        $this->scenarios[] = $event->scenarioId()->toString();
    }

    protected function whenScenarioWasRemoved(ScenarioWasRemoved $event): void
    {
        $this->scenarios = array_diff($this->scenarios, [$event->scenarioId()->toString()]);
    }

    protected function whenScenarioAnalysisNameWasChanged(ScenarioAnalysisNameWasChanged $event): void
    {
        $this->name = $event->name();
    }

    protected function whenScenarioAnalysisDescriptionWasChanged(ScenarioAnalysisDescriptionWasChanged $event): void
    {
        $this->description = $event->description();
    }

    protected function aggregateId(): string
    {
        return $this->id->toString();
    }
}
