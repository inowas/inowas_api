<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasAdded;
use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasRemoved;
use Inowas\Modflow\Model\Event\ModflowModelBoundingBoxWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelGridSizeWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelSoilModelIdWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Event\ModflowScenarioBoundaryWasAdded;
use Inowas\Modflow\Model\Event\ModflowScenarioBoundaryWasRemoved;
use Inowas\Modflow\Model\Event\ModflowScenarioWasCreated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowModel extends AggregateRoot
{
    /** @var  ModflowModelId */
    protected $modflowModelId;

    /** @var  UserId */
    protected $owner;

    /** @var ModflowModelName */
    protected $name;

    /** @var ModflowModelDescription */
    protected $description;

    /** @var ModflowModelGridSize */
    protected $gridSize;

    /** @var ModflowModelBoundingBox  */
    protected $boundingBox;

    /** @var AbstractModflowBoundary */
    protected $area;

    /** @var SoilModelId */
    protected $soilmodelId;

    /** @var array  */
    protected $boundaries;

    /** @var array  */
    protected $scenarios;

    #/** @var  \DateTime */
    #protected $start;

    #/** @var  \DateTime */
    #protected $end;

    #/** @var TimeUnit */
    #protected $timeUnit;

    public static function create(UserId $userId, ModflowId $modelId): ModflowModel
    {
        $self = new self();
        $self->owner = $userId;
        $self->modflowModelId = $modelId;
        $self->recordThat(ModflowModelWasCreated::byUserWithModflowId($userId, $modelId));
        return $self;
    }

    public function changeName(UserId $userId, ModflowModelName $name)
    {
        $this->name = $name;
        $this->recordThat(ModflowModelNameWasChanged::byUserWithName(
            $userId,
            $this->modflowModelId,
            $this->name
        ));
    }

    public function changeDescription(UserId $userId, ModflowModelDescription $description)
    {
        $this->description = $description;
        $this->recordThat(ModflowModelDescriptionWasChanged::withDescription(
            $userId,
            $this->modflowModelId,
            $this->description)
        );
    }

    public function changeGridSize(UserId $userId, ModflowModelGridSize $gridSize)
    {
        $this->gridSize = $gridSize;
        $this->recordThat(ModflowModelGridSizeWasChanged::withGridSize(
            $userId,
            $this->modflowModelId,
            $this->gridSize
        ));
    }

    public function changeBoundingBox(UserId $userId, ModflowModelBoundingBox $boundingBox)
    {
        $this->boundingBox = $boundingBox;
        $this->recordThat(ModflowModelBoundingBoxWasChanged::withBoundingBox(
            $userId,
            $this->modflowModelId,
            $this->boundingBox
        ));
    }

    public function addBoundary(ModflowBoundary $boundary)
    {
        if (! $this->containsBoundary($boundary->boundaryId())) {
            $this->boundaries[$boundary->boundaryId()->toString()] = $boundary;

            $this->recordThat(ModflowModelBoundaryWasAdded::withBoundary(
                $this->modflowModelId,
                $boundary
            ));
        }
    }

    public function addBoundaryToScenario(ScenarioId $scenarioId, ModflowBoundary $boundary)
    {
        if ($this->containsScenario($scenarioId)){
            /** @var ModflowScenario $scenario */
            $scenario = $this->scenarios[$scenarioId->toString()];

            if (! $scenario->containsBoundary($boundary->boundaryId())) {
                $scenario->boundaries[$boundary->boundaryId()->toString()] = $boundary;

                $this->recordThat(ModflowScenarioBoundaryWasAdded::toScenarioWithBoundary(
                    $this->modflowModelId,
                    $scenarioId,
                    $boundary
                ));
            }
        }
    }

    public function changeSoilmodelId(SoilModelId $soilModelId)
    {
        $this->soilmodelId = $soilModelId;
        $this->recordThat(ModflowModelSoilModelIdWasChanged::withSoilmodelId(
            $this->modflowModelId,
            $this->soilmodelId
        ));
    }

    public function removeBoundaryFromScenario(ScenarioId $scenarioId, BoundaryId $boundaryId)
    {
        if ($this->containsScenario($scenarioId)) {
            /** @var ModflowScenario $scenario */
            $scenario = $this->scenarios[$scenarioId->toString()];
            if ($scenario->containsBoundary($boundaryId)) {
                unset($scenario->boundaries[$boundaryId->toString()]);
                $this->recordThat(ModflowScenarioBoundaryWasRemoved::fromScenarioWithBoundary(
                    $this->modflowModelId,
                    $scenario->scenarioId(),
                    $boundaryId
                ));
            }
        }


    }

    public function removeBoundary(BoundaryId $boundaryId)
    {
        if ($this->containsBoundary($boundaryId)) {
            unset($this->boundaries[$boundaryId->toString()]);
            $this->recordThat(ModflowModelBoundaryWasRemoved::withBoundaryId(
                $this->modflowModelId,
                $boundaryId
            ));
        }
    }

    private function containsBoundary(BoundaryId $boundaryId): bool
    {
        return array_key_exists($boundaryId->toString(), $this->boundaries());
    }

    public function createScenario(UserId $userId, ScenarioId $scenarioId): void
    {
        if ($this->containsScenario($scenarioId)){
            throw new \Exception;
        }

        $this->scenarios[$scenarioId->toString()] = ModflowScenario::createFromModflowModel($userId, $scenarioId, $this);
        $this->recordThat(ModflowScenarioWasCreated::withId($userId, $this->modflowModelId, $scenarioId));
    }

    public function removeScenario(ModflowScenario $scenario): void
    {
        if ($this->containsScenario($scenario->scenarioId())){
            unset($this->scenarios[$scenario->scenarioId()->toString()]);
        }
    }

    private function containsScenario(ScenarioId $scenarioId): bool
    {
        return array_key_exists($scenarioId->toString(), $this->scenarios());
    }

    public function modflowModelId(): ModflowModelId
    {
        return $this->modflowModelId;
    }

    public function ownerId(): UserId
    {
        return $this->owner;
    }

    public function name(): ModflowModelName
    {
        return $this->name;
    }

    public function description(): ModflowModelDescription
    {
        return $this->description;
    }

    public function gridSize(): ModflowModelGridSize
    {
        return $this->gridSize;
    }

    public function boundingBox(): ModflowModelBoundingBox
    {
        return $this->boundingBox;
    }

    public function area(): AbstractModflowBoundary
    {
        return $this->area;
    }

    public function soilmodelId(): SoilModelId
    {
        return $this->soilmodelId;
    }

    public function boundaries(): array
    {
        if (! is_array($this->boundaries)){
            $this->boundaries = [];
        }

        return $this->boundaries;
    }

    public function scenarios(): array
    {
        if (! is_array($this->scenarios)) {
            $this->scenarios = array();
        }

        return $this->scenarios;
    }

    protected function whenModflowModelWasCreated(ModflowModelWasCreated $event)
    {
        $this->modflowModelId = $event->modflowModelId();
        $this->owner = $event->userId();
    }

    protected function whenModflowModelNameWasChanged(ModflowModelNameWasChanged $event)
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->name = $event->name();
        }
    }

    protected function whenModflowModelDescriptionWasChanged(ModflowModelDescriptionWasChanged $event)
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->description = $event->description();
        }
    }

    protected function whenModflowModelGridSizeWasChanged(ModflowModelGridSizeWasChanged $event)
    {
        if ($event->userId()->sameValueAs($this->ownerId())) {
            $this->gridSize = $event->gridSize();
        }
    }

    protected function whenModflowModelBoundingBoxWasChanged(ModflowModelBoundingBoxWasChanged $event)
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->boundingBox = $event->boundingBox();
        }
    }

    protected function whenModflowModelSoilModelIdWasChanged(ModflowModelSoilModelIdWasChanged $event)
    {
        $this->soilmodelId = $event->soilModelId();
    }

    protected function whenModflowModelBoundaryWasAdded(ModflowModelBoundaryWasAdded $event)
    {
        $boundary = $event->boundary();
        if ($boundary instanceof AreaBoundary){
            $this->area = $boundary;
        } else {
            if (! $this->containsBoundary($boundary->boundaryId())){
                $this->boundaries[$boundary->boundaryId()->toString()] = $boundary;
            }
        }
    }

    protected function whenModflowModelBoundaryWasRemoved(ModflowModelBoundaryWasRemoved $event)
    {
        if ($this->containsBoundary($event->boundaryId())){
            unset($this->boundaries[$event->boundaryId()->toString()]);
        }
    }

    protected function whenModflowScenarioWasCreated(ModflowScenarioWasCreated $event)
    {
        if (! $this->containsScenario($event->scenarioId())){
            $this->scenarios[$event->scenarioId()->toString()] = ModflowScenario::createFromModflowModel($event->userId(), $event->scenarioId(), $this);
        }
    }

    protected function whenModflowScenarioBoundaryWasAdded(ModflowScenarioBoundaryWasAdded $event)
    {
        if ($this->containsScenario($event->scenarioId())){
            /** @var ModflowScenario $scenario */
            $scenario = $this->scenarios[$event->scenarioId()->toString()];
            $boundary = $event->boundary();

            if (! $scenario->containsBoundary($boundary->boundaryId())){
                $scenario->boundaries[$boundary->boundaryId()->toString()] = $boundary;
            }
        }
    }

    protected function whenModflowScenarioBoundaryWasRemoved(ModflowScenarioBoundaryWasRemoved $event)
    {
        if ($this->containsScenario($event->scenarioId())){
            /** @var ModflowScenario $scenario */
            $scenario = $this->scenarios[$event->scenarioId()->toString()];
            $boundaryId = $event->boundaryId();

            if ($scenario->containsBoundary($boundaryId)){
                unset($scenario->boundaries[$boundaryId->toString()]);
            }
        }
    }

    protected function aggregateId(): string
    {
        return $this->modflowModelId->toString();
    }
}
