<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model;

use Inowas\Common\Boundaries\BoundaryMetadata;
use Inowas\Common\Boundaries\BoundaryName;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Polygon;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Boundaries\ModflowBoundary;
use Inowas\Common\Grid\BoundingBox;
use Inowas\Common\Grid\GridSize;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\ModelName;
use Inowas\Common\Modflow\ModelDescription;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\ParameterName;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\AMQP\CalculationResponse;
use Inowas\ModflowModel\Model\Event\AreaActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\AreaGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryActiveCellsWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryMetadataWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryNameWasUpdated;
use Inowas\ModflowModel\Model\Event\BoundaryObservationPointWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasAdded;
use Inowas\ModflowModel\Model\Event\BoundaryWasRemoved;
use Inowas\ModflowModel\Model\Event\BoundingBoxWasChanged;
use Inowas\ModflowModel\Model\Event\CalculationIdWasChanged;
use Inowas\ModflowModel\Model\Event\CalculationWasFinished;
use Inowas\ModflowModel\Model\Event\CalculationWasStarted;
use Inowas\ModflowModel\Model\Event\DescriptionWasChanged;
use Inowas\ModflowModel\Model\Event\FlowPackageWasChanged;
use Inowas\ModflowModel\Model\Event\GridSizeWasChanged;
use Inowas\ModflowModel\Model\Event\LengthUnitWasUpdated;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCloned;
use Inowas\ModflowModel\Model\Event\ModflowModelWasDeleted;
use Inowas\ModflowModel\Model\Event\ModflowPackageParameterWasUpdated;
use Inowas\ModflowModel\Model\Event\NameWasChanged;
use Inowas\ModflowModel\Model\Event\SoilModelWasChanged;
use Inowas\ModflowModel\Model\Event\ModflowModelWasCreated;
use Inowas\ModflowModel\Model\Event\StressPeriodsWereUpdated;
use Inowas\ModflowModel\Model\Event\TimeUnitWasUpdated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowModelAggregate extends AggregateRoot
{
    /** @var  ModflowId */
    protected $modelId;

    /** @var  UserId */
    protected $userId;

    /** @var SoilmodelId */
    protected $soilmodelId;

    /** @var array  */
    protected $boundaries;

    /** @var  CalculationId */
    protected $calculationId;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $modelId
     * @param UserId $userId
     * @param Polygon $polygon
     * @param GridSize $gridSize
     * @param BoundingBox $boundingBox
     * @param SoilmodelId $soilmodelId
     * @return ModflowModelAggregate
     * @internal param Area $area
     */
    public static function create(
        ModflowId $modelId,
        UserId $userId,
        Polygon $polygon,
        GridSize $gridSize,
        BoundingBox $boundingBox,
        SoilmodelId $soilmodelId
    ): ModflowModelAggregate
    {
        $self = new self();
        $self->modelId = $modelId;
        $self->userId = $userId;
        $self->soilmodelId = $soilmodelId;
        $self->boundaries = [];

        $self->recordThat(ModflowModelWasCreated::withParameters(
            $modelId,
            $userId,
            $polygon,
            $gridSize,
            $boundingBox,
            $soilmodelId
        ));

        return $self;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $newModelId
     * @param UserId $newUserId
     * @param SoilmodelId $soilmodelId
     * @param ModflowModelAggregate $model
     * @return ModflowModelAggregate
     */
    public static function clone(
        ModflowId $newModelId,
        UserId $newUserId,
        SoilmodelId $soilmodelId,
        ModflowModelAggregate $model
    ): ModflowModelAggregate
    {
        $self = new self();
        $self->modelId = $newModelId;
        $self->userId = $newUserId;
        $self->soilmodelId = $soilmodelId;
        $self->boundaries = $model->boundaries();

        $cloneSoilmodel = $soilmodelId !== $model->soilmodelId();

        $self->recordThat(ModflowModelWasCloned::fromModelAndUserWithParameters(
            $model->modflowModelId(),
            $self->modelId,
            $self->userId,
            $self->soilmodelId,
            $self->boundaries,
            $cloneSoilmodel
        ));

        return $self;
    }

    public function delete(UserId $userId): void
    {
        $this->recordThat(ModflowModelWasDeleted::byUserWitModelId(
            $this->modelId,
            $userId
        ));
    }

    public function calculationWasStarted(CalculationId $calculationId ): void
    {
        $this->recordThat(CalculationWasStarted::withId(
            $this->modelId,
            $calculationId
        ));
    }

    public function calculationWasFinished(CalculationResponse $response): void
    {
        $this->recordThat(CalculationWasFinished::withResponse(
            $this->modelId,
            $response
        ));
    }

    public function changeName(UserId $userId, ModelName $name): void
    {
        $this->recordThat(NameWasChanged::byUserWithName(
            $userId,
            $this->modelId,
            $name
        ));
    }

    public function changeDescription(UserId $userId, ModelDescription $description): void
    {
        $this->recordThat(DescriptionWasChanged::withDescription(
            $userId,
            $this->modelId,
            $description
        ));
    }

    public function changeBoundingBox(UserId $userId, BoundingBox $boundingBox): void
    {
        $this->recordThat(BoundingBoxWasChanged::withBoundingBox(
            $userId,
            $this->modelId,
            $boundingBox
        ));
    }

    public function changeGridSize(UserId $userId, GridSize $gridSize): void
    {
        $this->recordThat(GridSizeWasChanged::withGridSize(
            $userId,
            $this->modelId,
            $gridSize
        ));
    }

    public function changeFlowPackage(UserId $userId, PackageName $packageName): void
    {
        $this->recordThat(FlowPackageWasChanged::to(
            $userId,
            $this->modelId,
            $packageName
        ));
    }

    public function updateLengthUnit(UserId $userId, LengthUnit $lengthUnit): void
    {
        $this->recordThat(LengthUnitWasUpdated::withUnit(
            $userId,
            $this->modelId,
            $lengthUnit
        ));
    }

    public function updateTimeUnit(UserId $userId, TimeUnit $timeUnit): void
    {
        $this->recordThat(TimeUnitWasUpdated::withUnit(
            $userId,
            $this->modelId,
            $timeUnit
        ));
    }

    public function addBoundary(UserId $userId, ModflowBoundary $boundary): void
    {
        if (! in_array($boundary->boundaryId()->toString(), $this->boundaries, true)){
            $this->boundaries[] = $boundary->boundaryId()->toString();
            $this->recordThat(BoundaryWasAdded::to(
                $this->modelId,
                $userId,
                $boundary
            ));
        }
    }

    public function addObservationPointToBoundary(UserId $userId, BoundaryId $boundaryId, ObservationPoint $observationPoint): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->recordThat(BoundaryObservationPointWasAdded::byUserWithModflowAndBoundaryId(
                $userId,
                $this->modelId,
                $boundaryId,
                $observationPoint
            ));
        }
    }

    public function updateAreaGeometry(UserId $userId, Polygon $polygon): void
    {
        $this->recordThat(AreaGeometryWasUpdated::of(
            $this->modelId,
            $userId,
            $polygon
        ));
    }

    public function updateAreaActiveCells(UserId $userId, ActiveCells $activeCells): void
    {
        $this->recordThat(AreaActiveCellsWereUpdated::byUserAndModel(
            $userId,
            $this->modelId,
            $activeCells
        ));
    }

    public function updateBoundaryActiveCells(UserId $userId, BoundaryId $boundaryId, ActiveCells $activeCells): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->recordThat(BoundaryActiveCellsWereUpdated::of(
                $this->modelId,
                $userId,
                $boundaryId,
                $activeCells
            ));
        }
    }

    public function updateBoundaryAffectedLayers(UserId $userId, BoundaryId $boundaryId, AffectedLayers $affectedLayers): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->recordThat(BoundaryAffectedLayersWereUpdated::of(
                $this->modelId,
                $userId,
                $boundaryId,
                $affectedLayers
            ));
        }
    }

    public function updateBoundaryGeometry(UserId $userId, BoundaryId $boundaryId, Geometry $geometry): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->recordThat(BoundaryGeometryWasUpdated::of(
                $this->modelId,
                $userId,
                $boundaryId,
                $geometry
            ));
        }
    }

    public function updateBoundaryMetaData(UserId $userId, BoundaryId $boundaryId, BoundaryMetadata $metadata): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->recordThat(BoundaryMetadataWasUpdated::of(
                $this->modelId,
                $userId,
                $boundaryId,
                $metadata
            ));
        }
    }

    public function updateBoundaryName(UserId $userId, BoundaryId $boundaryId, BoundaryName $name): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->recordThat(BoundaryNameWasUpdated::of(
                $this->modelId,
                $userId,
                $boundaryId,
                $name
            ));
        }
    }

    public function updateCalculationId(CalculationId $calculationId): void
    {
        if ($this->calculationId->toString() !== $calculationId->toString()){
            $this->calculationId = $calculationId;
            $this->recordThat(CalculationIdWasChanged::withId($this->modelId, $calculationId));
        }
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param PackageName $packageName
     * @param ParameterName $parameterName
     * @param $data
     */
    public function updateModflowPackageParameter(UserId $userId, PackageName $packageName, ParameterName $parameterName, $data): void
    {
        $this->recordThat(ModflowPackageParameterWasUpdated::withProps(
            $userId,
            $this->modelId,
            $packageName,
            $parameterName,
            $data
        ));
    }

    public function updateStressPeriods(UserId $userId, StressPeriods $stressPeriods): void
    {
        $this->recordThat(StressPeriodsWereUpdated::of(
            $this->modelId,
            $userId,
            $stressPeriods
        ));
    }

    public function changeSoilmodelId(UserId $userId, SoilmodelId $soilModelId): void
    {
        $this->soilmodelId = $soilModelId;
        $this->recordThat(SoilModelWasChanged::withSoilmodelId(
            $userId,
            $this->modelId,
            $this->soilmodelId
        ));
    }

    public function removeBoundary(UserId $userId, BoundaryId $boundaryId): void
    {
        if (in_array($boundaryId->toString(), $this->boundaries, true)) {
            $this->boundaries = array_diff($this->boundaries, [$boundaryId->toString()]);
            $this->recordThat(BoundaryWasRemoved::withBoundaryId(
                $userId,
                $this->modelId,
                $boundaryId
            ));
        }
    }

    public function modflowModelId(): ModflowId
    {
        return $this->modelId;
    }

    public function userId(): UserId
    {
        return $this->userId;
    }

    public function soilmodelId(): SoilmodelId
    {
        return $this->soilmodelId;
    }

    public function boundaries(): array
    {
        return $this->boundaries;
    }

    public function calculationId(): CalculationId
    {
        return $this->calculationId;
    }

    protected function whenAreaActiveCellsWereUpdated(AreaActiveCellsWereUpdated $event): void
    {}

    protected function whenAreaGeometryWasUpdated(AreaGeometryWasUpdated $event): void
    {}

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

    protected function whenBoundaryObservationPointWasAdded(BoundaryObservationPointWasAdded $event): void
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
    {}

    protected function whenCalculationIdWasChanged(CalculationIdWasChanged $event): void
    {
        $this->calculationId = $event->calculationId();
    }

    protected function whenCalculationWasFinished(CalculationWasFinished $event): void
    {}

    protected function whenCalculationWasStarted(CalculationWasStarted $event): void
    {}

    protected function whenDescriptionWasChanged(DescriptionWasChanged $event): void
    {}

    protected function whenFlowPackageWasChanged(FlowPackageWasChanged $event): void
    {}

    protected function whenGridSizeWasChanged(GridSizeWasChanged $event): void
    {}

    protected function whenLengthUnitWasUpdated(LengthUnitWasUpdated $event): void
    {}

    protected function whenModflowModelWasCloned(ModflowModelWasCloned $event): void
    {
        $this->modelId = $event->modelId();
        $this->userId = $event->userId();
        $this->soilmodelId = $event->soilmodelId();
        $this->boundaries = $event->boundaryIds();
        $this->calculationId = CalculationId::fromString('');
    }

    protected function whenModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->modelId = $event->modelId();
        $this->userId = $event->userId();
        $this->soilmodelId = $event->soilmodelId();
        $this->boundaries = [];
        $this->calculationId = CalculationId::fromString('');
    }

    protected function whenModflowModelWasDeleted(ModflowModelWasDeleted $event): void
    {}

    protected function whenModflowPackageParameterWasUpdated(ModflowPackageParameterWasUpdated $event): void
    {}

    protected function whenNameWasChanged(NameWasChanged $event): void
    {}

    protected function whenSoilModelWasChanged(SoilModelWasChanged $event): void
    {
        $this->soilmodelId = $event->soilModelId();
    }

    protected function whenStressPeriodsWereUpdated(StressPeriodsWereUpdated $event): void
    {}

    protected function whenTimeUnitWasUpdated(TimeUnitWasUpdated $event): void
    {}

    protected function aggregateId(): string
    {
        return $this->modelId->toString();
    }
}
