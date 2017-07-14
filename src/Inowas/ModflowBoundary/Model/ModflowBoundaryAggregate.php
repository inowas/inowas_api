<?php

declare(strict_types=1);

namespace Inowas\ModflowBoundary\Model;

use Inowas\Common\Boundaries\Metadata;
use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\Name;
use Inowas\ModflowBoundary\Model\Event\BoundaryAffectedLayersWereUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryGeometryWasUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryMetadataWasUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryNameWasUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryObservationPointWasAdded;
use Inowas\ModflowBoundary\Model\Event\BoundaryObservationPointWasRemoved;
use Inowas\ModflowBoundary\Model\Event\BoundaryObservationPointWasUpdated;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasCloned;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasAdded;
use Inowas\ModflowBoundary\Model\Event\BoundaryWasRemoved;
use Prooph\EventSourcing\AggregateRoot;

class ModflowBoundaryAggregate extends AggregateRoot
{
    /** @var  BoundaryId */
    protected $boundaryId;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param ModflowId $modelId
     * @param UserId $userId
     * @param BoundaryType $boundaryType
     * @param Name $boundaryName
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param Metadata $metadata
     * @return ModflowBoundaryAggregate
     */
    public static function create(
        BoundaryId $boundaryId,
        ModflowId $modelId,
        UserId $userId,
        BoundaryType $boundaryType,
        Name $boundaryName,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        Metadata $metadata
    ): ModflowBoundaryAggregate
    {
        $self = new self();
        $self->boundaryId = $boundaryId;

        $self->recordThat(BoundaryWasAdded::toModelWithParameters(
            $boundaryId,
            $modelId,
            $userId,
            $boundaryType,
            $boundaryName,
            $geometry,
            $affectedLayers,
            $metadata
        ));

        return $self;
    }

    /**
     * @param BoundaryId $newBoundaryId
     * @param ModflowId $modelId
     * @param ModflowBoundaryAggregate $aggregate
     * @return ModflowBoundaryAggregate
     */
    public static function clone(
        BoundaryId $newBoundaryId,
        ModflowId $modelId,
        ModflowBoundaryAggregate $aggregate
    ): ModflowBoundaryAggregate
    {
        $self = new self();
        $self->boundaryId = $newBoundaryId;

        $self->recordThat(BoundaryWasCloned::withParameters(
            $newBoundaryId,
            $aggregate->boundaryId,
            $modelId
        ));

        return $self;
    }

    public function remove(ModflowId $modflowId, UserId $userId): void
    {
        $this->recordThat(BoundaryWasRemoved::fromModelWithId(
            $this->boundaryId,
            $modflowId,
            $userId
        ));
    }

    public function updateBoundaryAffectedLayers(UserId $userId, AffectedLayers $affectedLayers): void
    {
        $this->recordThat(BoundaryAffectedLayersWereUpdated::of(
            $this->boundaryId,
            $userId,
            $affectedLayers
        ));
    }

    public function updateGeometry(UserId $userId, Geometry $geometry): void
    {
        $this->recordThat(BoundaryGeometryWasUpdated::of(
            $this->boundaryId,
            $userId,
            $geometry
        ));
    }

    public function updateBoundaryName(UserId $userId, Name $name): void
    {
        $this->recordThat(BoundaryNameWasUpdated::of(
            $this->boundaryId,
            $userId,
            $name
        ));
    }

    public function updateMetaData(UserId $userId, Metadata $metadata): void
    {
        $this->recordThat(BoundaryMetadataWasUpdated::of(
            $this->boundaryId,
            $userId,
            $metadata
        ));
    }

    public function addObservationPoint(UserId $userId, ObservationPoint $observationPoint): void
    {
        $this->recordThat(BoundaryObservationPointWasAdded::addedByUserWithData(
            $this->boundaryId,
            $userId,
            $observationPoint
        ));
    }

    public function removeObservationPoint(UserId $userId, ObservationPointId $observationPointId): void
    {
        $this->recordThat(BoundaryObservationPointWasRemoved::byUserWithId(
            $this->boundaryId,
            $userId,
            $observationPointId
        ));
    }

    public function deleteObservationPoint(UserId $userId, ObservationPointId $observationPointId): void
    {
        $this->recordThat(BoundaryObservationPointWasRemoved::byUserWithId(
            $this->boundaryId,
            $userId,
            $observationPointId
        ));
    }

    public function updateObservationPoint(UserId $userId, ObservationPoint $observationPoint): void
    {
        $this->recordThat(BoundaryObservationPointWasUpdated::updatedByUserWithData(
            $this->boundaryId,
            $userId,
            $observationPoint
        ));
    }

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }

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

    protected function whenBoundaryObservationPointWasRemoved(BoundaryObservationPointWasRemoved $event): void
    {}

    protected function whenBoundaryObservationPointWasUpdated(BoundaryObservationPointWasUpdated $event): void
    {}

    protected function whenBoundaryWasAdded(BoundaryWasAdded $event): void
    {
        $this->boundaryId = $event->boundaryId();
    }

    protected function whenBoundaryWasCloned(BoundaryWasCloned $event): void
    {
        $this->boundaryId = $event->boundaryId();
    }

    protected function whenBoundaryWasRemoved(BoundaryWasRemoved $event): void
    {}

    protected function aggregateId(): string
    {
        return $this->boundaryId->toString();
    }
}
