<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\Common\Boundaries\AbstractBoundary;
use Inowas\Common\Boundaries\Area;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Modelname;
use Inowas\Common\Modflow\ModflowModelDescription;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\Event\ActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundaryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\DescriptionWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\ModflowModel\Model\Event\SoilModelIdWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowModelAggregate extends AggregateRoot
{
    /** @var  ModflowId */
    protected $modflowId;

    /** @var  UserId */
    protected $owner;

    /** @var  bool */
    protected $public;

    /** @var Modelname */
    protected $name;

    /** @var ModflowModelDescription */
    protected $description;

    /** @var GridSize */
    protected $gridSize;

    /** @var BoundingBox  */
    protected $boundingBox;

    /** @var Area */
    protected $area;

    /** @var SoilmodelId */
    protected $soilmodelId;

    /** @var array  */
    protected $boundaries;

    /** @var  LengthUnit */
    protected $lengthUnit;

    /** @var  TimeUnit */
    protected $timeUnit;

    public static function create(ModflowId $modflowId, UserId $userId,  Area $area, GridSize $gridSize, BoundingBox $boundingBox, LengthUnit $lengthUnit, TimeUnit $timeUnit): ModflowModelAggregate
    {
        $self = new self();
        $self->modflowId = $modflowId;
        $self->owner = $userId;
        $self->area = $area;
        $self->gridSize = $gridSize;
        $self->boundingBox = $boundingBox;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        $self->boundaries = [];

        $self->recordThat(ModflowModelWasCreated::withParameters($modflowId, $userId, $area, [], $gridSize, $boundingBox, $lengthUnit, $timeUnit));
        return $self;
    }

    public function createCopyWithNewIdAndUserId(ModflowId $modflowId, UserId $userId): ModflowModelAggregate
    {
        $self = new self();
        $self->modflowId = $modflowId;
        $self->owner = $userId;
        $self->area = $this->area;
        $self->gridSize = $this->gridSize;
        $self->boundingBox = $this->boundingBox;
        $self->lengthUnit = $this->lengthUnit;
        $self->timeUnit = $this->timeUnit;
        $self->boundaries = $this->boundaries;

        $self->recordThat(ModflowModelWasCreated::withParameters($modflowId, $userId, $this->area, $this->boundaries, $this->gridSize, $this->boundingBox, $this->lengthUnit, $this->timeUnit));
        return $self;
    }

    public function changeName(Modelname $name): void
    {
        $this->name = $name;
    }

    public function changeModelName(UserId $userId, Modelname $name)
    {
        $this->name = $name;
        $this->recordThat(NameWasChanged::byUserWithName(
            $userId,
            $this->modflowId,
            $this->name
        ));
    }

    public function changeDescription(ModflowModelDescription $description): void
    {
        $this->description = $description;
    }

    public function changeModelDescription(UserId $userId, ModflowModelDescription $description)
    {
        $this->description = $description;
        $this->recordThat(DescriptionWasChanged::withDescription(
            $userId,
            $this->modflowId,
            $this->description)
        );
    }

    public function changeGridSize(UserId $userId, GridSize $gridSize)
    {
        $this->gridSize = $gridSize;
        $this->recordThat(GridSizeWasChanged::withGridSize(
            $userId,
            $this->modflowId,
            $this->gridSize
        ));
    }

    public function changeBoundingBox(UserId $userId, BoundingBox $boundingBox)
    {
        $this->boundingBox = $boundingBox;
        $this->recordThat(BoundingBoxWasChanged::withBoundingBox(
            $userId,
            $this->modflowId,
            $this->boundingBox
        ));
    }

    public function updateAreaGeometry(UserId $userId, Polygon $polygon)
    {
        $this->area = $this->area->updateGeometry($polygon);
        $this->recordThat(AreaGeometryWasUpdated::of(
            $this->modflowId,
            $userId,
            $polygon
        ));
    }

    public function addBoundaryToModel(UserId $userId, ModflowBoundary $boundary): void
    {
        if (! array_key_exists($boundary->boundaryId()->toString(), $this->boundaries())){

            $this->boundaries[$boundary->boundaryId()->toString()] = true;

            $this->recordThat(BoundaryWasAdded::to(
                $this->modflowId,
                $userId,
                $boundary
            ));
        }
    }

    public function updateBoundary(UserId $userId, ModflowBoundary $boundary): void
    {
        if (array_key_exists($boundary->boundaryId()->toString(), $this->boundaries())) {

            $this->boundaries[$boundary->boundaryId()->toString()] = true;

            $this->recordThat(BoundaryWasUpdated::byUserWithBaseModelId(
                $userId,
                $this->modflowId,
                $boundary
            ));
        }
    }

    public function updateBoundaryGeometry(UserId $userId, BoundaryId $boundaryId, Geometry $geometry): void
    {
        if (array_key_exists($boundaryId->toString(), $this->boundaries())) {
            $this->recordThat(BoundaryGeometryWasUpdated::of(
                $this->modflowId,
                $userId,
                $boundaryId,
                $geometry
            ));
        }
    }

    public function changeSoilmodelId(SoilmodelId $soilModelId): void
    {
        $this->soilmodelId = $soilModelId;
        $this->recordThat(SoilModelIdWasChanged::withSoilmodelId(
            $this->modflowId,
            $this->soilmodelId
        ));
    }

    public function removeBoundary(UserId $userId, BoundaryId $boundaryId)
    {
        if (array_key_exists($boundaryId->toString(), $this->boundaries())) {
            unset($this->boundaries[$boundaryId->toString()]);
            $this->recordThat(BoundaryWasRemoved::withBoundaryId(
                $userId,
                $this->modflowId,
                $boundaryId
            ));
        }
    }

    public function updateActiveCells(UserId $userId, BoundaryId $boundaryId, string $boundaryType, ActiveCells $activeCells)
    {
        if ($this->area->boundaryId()->sameValueAs($boundaryId)){
            $this->area = $this->area->setActiveCells($activeCells);
        }

        if (array_key_exists($boundaryId->toString(), $this->boundaries())){
            /** @var AbstractBoundary $boundary */
            $boundary = $this->boundaries[$boundaryId->toString()];
            $boundary[$boundaryId->toString()] = $boundary->setActiveCells($activeCells);
        }

        $this->recordThat(ActiveCellsWereUpdated::ofBoundary(
            $userId,
            $this->modflowId,
            $boundaryId,
            $boundaryType,
            $activeCells
        ));
    }

    public function modflowModelId(): ModflowId
    {
        return $this->modflowId;
    }

    public function ownerId(): UserId
    {
        return $this->owner;
    }

    public function name(): Modelname
    {
        if ($this->name === null){
            $this->name = Modelname::fromString('');
        }
        return $this->name;
    }

    public function description(): ModflowModelDescription
    {
        if ($this->description === null){
            $this->description = ModflowModelDescription::fromString('');
        }
        return $this->description;
    }

    public function gridSize(): GridSize
    {
        return $this->gridSize;
    }

    public function boundingBox(): BoundingBox
    {
        return $this->boundingBox;
    }

    public function area(): Area
    {
        return $this->area;
    }

    public function soilmodelId(): SoilmodelId
    {
        return $this->soilmodelId;
    }

    public function boundaries(): array
    {
        return $this->boundaries;
    }

    public function lengthUnit(): LengthUnit
    {
        return $this->lengthUnit;
    }

    public function timeUnit(): TimeUnit
    {
        return $this->timeUnit;
    }

    protected function whenModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->modflowId = $event->modelId();
        $this->owner = $event->userId();
        $this->area = $event->area();
        $this->boundaries = $event->boundaries();
        $this->gridSize = $event->gridSize();
        $this->boundingBox = $event->boundingBox();
        $this->lengthUnit = $event->lengthUnit();
        $this->timeUnit = $event->timeUnit();
    }

    protected function whenAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $this->area = $this->area->updateGeometry($event->geometry());
    }

    protected function whenNameWasChanged(NameWasChanged $event): void
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->name = $event->name();
        }
    }

    protected function whenDescriptionWasChanged(DescriptionWasChanged $event): void
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->description = $event->description();
        }
    }

    protected function whenGridSizeWasChanged(GridSizeWasChanged $event): void
    {
        if ($event->userId()->sameValueAs($this->ownerId())) {
            $this->gridSize = $event->gridSize();
        }
    }

    protected function whenBoundingBoxWasChanged(BoundingBoxWasChanged $event): void
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->boundingBox = $event->boundingBox();
        }
    }

    protected function whenSoilModelIdWasChanged(SoilModelIdWasChanged $event): void
    {
        $this->soilmodelId = $event->soilModelId();
    }

    protected function whenBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $boundary = $event->boundary();
        $this->boundaries[$boundary->boundaryId()->toString()] = $boundary;
    }

    protected function whenBoundaryWasUpdated(BoundaryWasUpdated $event): void
    {
        $boundary = $event->boundary();
        $this->boundaries[$boundary->boundaryId()->toString()] = $boundary;
    }

    protected function whenBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {

    }

    protected function whenBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        unset($this->boundaries[$event->boundaryId()->toString()]);
    }


    protected function whenActiveCellsWereUpdated(ActiveCellsWereUpdated $event): void
    {
        if ($this->area->boundaryId()->sameValueAs($event->boundaryId())){
            $this->area = $this->area->setActiveCells($event->activeCells());
            return;
        }

        $boundary = $this->boundaries[$event->boundaryId()->toString()];
        $boundary[$event->boundaryId()->toString()] = $boundary->setActiveCells($event->activeCells());
    }

    protected function aggregateId(): string
    {
        return $this->modflowId->toString();
    }
}
