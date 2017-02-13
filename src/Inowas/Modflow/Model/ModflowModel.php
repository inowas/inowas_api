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
use Inowas\Modflow\Model\Event\ModflowScenarioWasCreated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowModel extends AggregateRoot
{
    /** @var  ModflowModelId */
    protected $modflowModelId;

    /** @var ModflowModelName */
    protected $name;

    /** @var ModflowModelDescription */
    protected $description;

    /** @var ModflowModelGridSize */
    protected $gridSize;

    /** @var ModflowModelBoundingBox  */
    protected $boundingBox;

    /** @var ModflowBoundary */
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

    public static function create(ModflowId $modelId): ModflowModel
    {
        $self = new self();
        $self->modflowModelId = $modelId;
        $self->recordThat(ModflowModelWasCreated::withId($modelId));
        return $self;
    }

    public function changeName(ModflowModelName $name)
    {
        $this->name = $name;
        $this->recordThat(ModflowModelNameWasChanged::withName(
            $this->modflowModelId,
            $this->name
        ));
    }

    public function changeDescription(ModflowModelDescription $description)
    {
        $this->description = $description;
        $this->recordThat(ModflowModelDescriptionWasChanged::withDescription(
            $this->modflowModelId,
            $this->description)
        );
    }

    public function changeGridSize(ModflowModelGridSize $gridSize)
    {
        $this->gridSize = $gridSize;
        $this->recordThat(ModflowModelGridSizeWasChanged::withGridSize(
            $this->modflowModelId,
            $this->gridSize
        ));
    }

    public function changeBoundingBox(ModflowModelBoundingBox $boundingBox)
    {
        $this->boundingBox = $boundingBox;
        $this->recordThat(ModflowModelBoundingBoxWasChanged::withBoundingBox(
            $this->modflowModelId,
            $this->boundingBox
        ));
    }

    public function addBoundary(BoundaryId $boundaryId, BoundaryType $boundaryType)
    {
        if ($boundaryType->type() == $boundaryType::AREA) {
            $this->area = AreaBoundary::create($boundaryId);
        }

        $this->recordThat(ModflowModelBoundaryWasAdded::withIdAndType(
            $this->modflowModelId,
            $boundaryId,
            $boundaryType
        ));
    }

    public function changeSoilmodelId(SoilModelId $soilModelId)
    {
        $this->soilmodelId = $soilModelId;
        $this->recordThat(ModflowModelSoilModelIdWasChanged::withSoilmodelId(
            $this->modflowModelId,
            $this->soilmodelId
        ));
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

    public function createScenario(ScenarioId $scenarioId): void
    {
        if ($this->containsScenario($scenarioId)){
            throw new \Exception;
        }

        $this->scenarios[$scenarioId->toString()] = ModflowScenario::createFromModflowModel($scenarioId, $this);
        $this->recordThat(ModflowScenarioWasCreated::withId($this->modflowModelId, $scenarioId));
    }

    public function removeScenario(ModflowScenario $scenario): void
    {
        if ($this->containsScenario($scenario)){
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

    public function area(): ModflowBoundary
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
    }

    protected function whenModflowModelNameWasChanged(ModflowModelNameWasChanged $event)
    {
        $this->name = $event->name();
    }

    protected function whenModflowModelDescriptionWasChanged(ModflowModelDescriptionWasChanged $event)
    {
        $this->description = $event->description();
    }

    protected function whenModflowModelGridSizeWasChanged(ModflowModelGridSizeWasChanged $event)
    {
        $this->gridSize = $event->gridSize();
    }

    protected function whenModflowModelBoundingBoxWasChanged(ModflowModelBoundingBoxWasChanged $event)
    {
        $this->boundingBox = $event->boundingBox();
    }

    protected function whenModflowModelSoilModelIdWasChanged(ModflowModelSoilModelIdWasChanged $event)
    {
        $this->soilmodelId = $event->soilModelId();
    }

    protected function whenModflowModelBoundaryWasAdded(ModflowModelBoundaryWasAdded $event)
    {
        $boundary = ModflowBoundaryFactory::createFromIdAndType($event->boundaryId(), $event->boundaryType());

        if ($event->boundaryType()->type() == BoundaryType::AREA){
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
            $this->scenarios[$event->scenarioId()->toString()] = ModflowScenario::createFromModflowModel($event->scenarioId(), $this);
        }
    }

    protected function aggregateId(): string
    {
        return $this->modflowModelId->toString();
    }
}
