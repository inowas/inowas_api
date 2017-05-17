<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\Common\Boundaries\Area;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
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
use Inowas\ModflowModel\Model\Event\AreaActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryMetadataWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryNameWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\DescriptionWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\LengthUnitWasUpdated;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\ModflowModel\Model\Event\SoilModelIdWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ModflowModel\Model\Event\TimeUnitWasUpdated;
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

    public static function create(ModflowId $modflowId, UserId $userId, SoilmodelId $soilmodelId, Area $area, GridSize $gridSize, BoundingBox $boundingBox, LengthUnit $lengthUnit, TimeUnit $timeUnit): ModflowModelAggregate
    {
        $self = new self();
        $self->modflowId = $modflowId;
        $self->owner = $userId;
        $self->soilmodelId = $soilmodelId;
        $self->area = $area;
        $self->gridSize = $gridSize;
        $self->boundingBox = $boundingBox;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        $self->boundaries = [];

        $self->recordThat(ModflowModelWasCreated::withParameters($modflowId, $userId, $soilmodelId, $area, [], $gridSize, $boundingBox, $lengthUnit, $timeUnit));
        return $self;
    }

    public static function cloneWithIdUserAndAggregate(ModflowId $newModelId, UserId $newUserId, ModflowModelAggregate $model): ModflowModelAggregate
    {
        $self = new self();
        $self->modflowId = $newModelId;
        $self->owner = $newUserId;
        $self->soilmodelId = $model->soilmodelId();
        $self->area = $model->area();
        $self->boundaries = $model->boundaries();
        $self->gridSize = $model->gridSize();
        $self->boundingBox = $model->boundingBox();
        $self->lengthUnit = $model->lengthUnit();
        $self->timeUnit = $model->timeUnit();
        $self->boundaries = $model->boundaries();

        $self->recordThat(ModflowModelWasCloned::fromModelAndUserWithParameters(
            $model->modflowModelId(),
            $model->ownerId(),
            $self->modflowId,
            $self->owner,
            $self->soilmodelId,
            $self->area,
            $self->boundaries,
            $self->gridSize,
            $self->boundingBox,
            $self->lengthUnit,
            $self->timeUnit
        ));

        return $self;
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

    public function changeModelDescription(UserId $userId, ModflowModelDescription $description)
    {
        $this->description = $description;
        $this->recordThat(DescriptionWasChanged::withDescription(
            $userId,
            $this->modflowId,
            $this->description)
        );
    }

    public function changeAreaGeometry(UserId $userId, Polygon $polygon)
    {
        $this->area = $this->area->updateGeometry($polygon);
        $this->recordThat(AreaGeometryWasUpdated::of(
            $this->modflowId,
            $userId,
            $polygon
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

    public function changeDescription(ModflowModelDescription $description): void
    {
        $this->description = $description;
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

    public function updateLengthUnit(UserId $userId, LengthUnit $lengthUnit)
    {
        $this->lengthUnit = $lengthUnit;
        $this->recordThat(LengthUnitWasUpdated::withUnit(
            $userId,
            $this->modflowId,
            $this->lengthUnit
        ));
    }

    public function updateTimeUnit(UserId $userId, TimeUnit $timeUnit)
    {
        $this->timeUnit = $timeUnit;
        $this->recordThat(TimeUnitWasUpdated::withUnit(
            $userId,
            $this->modflowId,
            $this->timeUnit
        ));
    }

    public function addBoundaryToModel(UserId $userId, ModflowBoundary $boundary): void
    {
        if (! in_array($boundary->boundaryId()->toString(), $this->boundaries)){
            $this->boundaries[] = $boundary->boundaryId()->toString();
            $this->recordThat(BoundaryWasAdded::to(
                $this->modflowId,
                $userId,
                $boundary
            ));
        }
    }

    public function updateAreaActiveCells(UserId $userId, ActiveCells $activeCells): void
    {
        $this->recordThat(AreaActiveCellsWereUpdated::byUserAndModel(
            $userId,
            $this->modflowId,
            $activeCells
        ));

    }

    public function updateBoundaryActiveCells(UserId $userId, BoundaryId $boundaryId, ActiveCells $activeCells): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries)) {
            $this->recordThat(BoundaryActiveCellsWereUpdated::of(
                $this->modflowId,
                $userId,
                $boundaryId,
                $activeCells
            ));
        }
    }

    public function updateBoundaryAffectedLayers(UserId $userId, BoundaryId $boundaryId, AffectedLayers $affectedLayers): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries)) {
            $this->recordThat(BoundaryAffectedLayersWereUpdated::of(
                $this->modflowId,
                $userId,
                $boundaryId,
                $affectedLayers
            ));
        }
    }

    public function updateBoundaryGeometry(UserId $userId, BoundaryId $boundaryId, Geometry $geometry): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries)) {
            $this->recordThat(BoundaryGeometryWasUpdated::of(
                $this->modflowId,
                $userId,
                $boundaryId,
                $geometry
            ));
        }
    }

    public function updateBoundaryMetaData(UserId $userId, BoundaryId $boundaryId, array $metadata): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries)) {
            $this->recordThat(BoundaryMetadataWasUpdated::of(
                $this->modflowId,
                $userId,
                $boundaryId,
                $metadata
            ));
        }
    }

    public function updateBoundaryName(UserId $userId, BoundaryId $boundaryId, BoundaryName $name): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries)) {
            $this->recordThat(BoundaryNameWasUpdated::of(
                $this->modflowId,
                $userId,
                $boundaryId,
                $name
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
        if (in_array($boundaryId->toString(), $this->boundaries())) {
            $this->boundaries = array_diff($this->boundaries, [$boundaryId->toString()]);
            $this->recordThat(BoundaryWasRemoved::withBoundaryId(
                $userId,
                $this->modflowId,
                $boundaryId
            ));
        }
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

    protected function whenAreaActiveCellsWereUpdated(AreaActiveCellsWereUpdated $event): void
    {
        $this->area = $this->area->updateActiveCells($event->activeCells());
    }

    protected function whenAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {
        $this->area = $this->area->updateGeometry($event->geometry());
    }

    protected function whenBoundaryActiveCellsWereUpdated(BoundaryActiveCellsWereUpdated $event): void
    {}

    protected function whenBoundaryAffectedLayersWereUpdated(BoundaryAffectedLayersWereUpdated $event): void
    {}

    protected function whenBoundaryGeometryWasUpdated(BoundaryGeometryWasUpdated $event): void
    {}

    protected function whenBoundaryMetadataWasUpdated(BoundaryMetadataWasUpdated $event): void
    {}

    protected function whenBoundaryNameWasUpdated(BoundaryNameWasUpdated $event): void
    {}

    protected function whenBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $this->boundaries[] = $event->boundary()->boundaryId()->toString();
    }

    protected function whenBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {
        $this->boundaries = array_diff($this->boundaries, [$event->boundaryId()->toString()]);
    }

    protected function whenBoundingBoxWasChanged(BoundingBoxWasChanged $event): void
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->boundingBox = $event->boundingBox();
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

    protected function whenLengthUnitWasUpdated(LengthUnitWasUpdated $event): void
    {
        $this->lengthUnit = $event->lengthUnit();
    }

    protected function whenModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        $this->modflowId = $event->modelId();
        $this->owner = $event->userId();
        $this->area = $event->area();
        $this->soilmodelId = $event->soilmodelId();
        $this->boundaries = $event->boundaryIds();
        $this->gridSize = $event->gridSize();
        $this->boundingBox = $event->boundingBox();
        $this->lengthUnit = $event->lengthUnit();
        $this->timeUnit = $event->timeUnit();
    }

    protected function whenModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->modflowId = $event->modelId();
        $this->owner = $event->userId();
        $this->soilmodelId = $event->soilmodelId();
        $this->area = $event->area();
        $this->boundaries = $event->boundaries();
        $this->gridSize = $event->gridSize();
        $this->boundingBox = $event->boundingBox();
        $this->lengthUnit = $event->lengthUnit();
        $this->timeUnit = $event->timeUnit();
    }

    protected function whenNameWasChanged(NameWasChanged $event): void
    {
        if ($event->userId()->sameValueAs($this->ownerId())){
            $this->name = $event->name();
        }
    }

    protected function whenSoilModelIdWasChanged(SoilModelIdWasChanged $event): void
    {
        $this->soilmodelId = $event->soilModelId();
    }

    protected function whenTimeUnitWasUpdated(TimeUnitWasUpdated $event): void
    {
        $this->timeUnit = $event->timeUnit();
    }

    protected function aggregateId(): string
    {
        return $this->modflowId->toString();
    }
}
