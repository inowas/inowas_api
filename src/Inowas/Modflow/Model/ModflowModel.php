<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Inowas\Modflow\Model\Event\ModflowModelAreaIdWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasAdded;
use Inowas\Modflow\Model\Event\ModflowModelBoundaryWasRemoved;
use Inowas\Modflow\Model\Event\ModflowModelBoundingBoxWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelDescriptionWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelGridSizeWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelNameWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelSoilModelIdWasChanged;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowModel extends AggregateRoot
{
    /** @var  ModflowModelId */
    private $modflowModelId;

    /** @var ModflowModelName */
    private $name;

    /** @var ModflowModelDescription */
    private $description;

    /** @var ModflowModelGridSize */
    private $gridSize;

    /** @var ModflowModelBoundingBox  */
    private $boundingBox;

    /** @var BoundaryId */
    private $areaId;

    /** @var SoilModelId */
    private $soilmodelId;

    /** @var Collection  */
    private $boundaries;

    #/** @var  \DateTime */
    #private $start;

    #/** @var  \DateTime */
    #private $end;

    #/** @var TimeUnit */
    #private $timeUnit;

    public static function create(ModflowModelId $modelId): ModflowModel
    {
        $self = new self();
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

    public function changeAreaId(BoundaryId $areaId)
    {
        $this->areaId = $areaId;
        $this->recordThat(ModflowModelAreaIdWasChanged::withAreaId(
            $this->modflowModelId,
            $this->areaId
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

    public function addBoundary(BoundaryId $boundaryId)
    {
        if ($this->boundaries === null){
            $this->boundaries = new ArrayCollection();
        }

        if (! $this->boundaries->contains($boundaryId)){
            $this->boundaries->add($boundaryId);
            $this->recordThat(ModflowModelBoundaryWasAdded::withBoundaryId(
                $this->modflowModelId,
                $boundaryId
            ));
        }
    }

    public function removeBoundary(BoundaryId $boundaryId)
    {
        if ($this->boundaries === null){
            $this->boundaries = new ArrayCollection();
        }

        foreach ($this->boundaries as $key => $boundary){
            if ($boundary->toString() == $boundaryId->toString()){
                $this->boundaries->remove($key);

                $this->recordThat(ModflowModelBoundaryWasRemoved::withBoundaryId(
                    $this->modflowModelId,
                    $boundaryId
                ));
            }
        }
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

    public function areaId(): BoundaryId
    {
        return $this->areaId;
    }

    public function soilmodelId(): SoilModelId
    {
        return $this->soilmodelId;
    }

    public function boundaries(): Collection
    {
        if ($this->boundaries === null){
            $this->boundaries = new ArrayCollection();
        }

        return $this->boundaries;
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

    protected function whenModflowModelAreaIdWasChanged(ModflowModelAreaIdWasChanged $event)
    {
        $this->areaId = $event->areaId();
    }

    protected function whenModflowModelSoilModelIdWasChanged(ModflowModelSoilModelIdWasChanged $event)
    {
        $this->soilmodelId = $event->soilModelId();
    }

    protected function whenModflowModelBoundaryWasAdded(ModflowModelBoundaryWasAdded $event)
    {
        if ($this->boundaries === null){
            $this->boundaries = new ArrayCollection();
        }

        if (! $this->boundaries->contains($event->boundaryId())){
            $this->boundaries->add($event->boundaryId());
        }
    }

    protected function whenModflowModelBoundaryWasRemoved(ModflowModelBoundaryWasRemoved $event)
    {
        if ($this->boundaries === null){
            $this->boundaries = new ArrayCollection();
        }

        foreach ($this->boundaries as $key => $boundary){
            if ($boundary->toString() == $event->boundaryId()->toString()) {
                $this->boundaries->remove($key);
            }
        }
    }

    protected function aggregateId(): string
    {
        return $this->modflowModelId->toString();
    }
}
