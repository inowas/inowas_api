<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class ModflowBoundary
{
    CONST CARDINALITY = '';
    CONST TYPE = '';

    /** @var  BoundaryId */
    protected $boundaryId;

    /** @var  BoundaryName */
    protected $name;

    /** @var  Geometry */
    protected $geometry;

    /** @var  AffectedLayers */
    protected $affectedLayers;

    /** @var  Metadata */
    protected $metadata;

    /** @var ObservationPointCollection  */
    protected $observationPoints;

    protected function self(): ModflowBoundary
    {
        $self = new static($this->boundaryId, $this->name, $this->geometry, $this->affectedLayers, $this->metadata);
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param BoundaryName $name
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param Metadata $metadata
     * @return ModflowBoundary
     */
    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        Metadata $metadata
    ): ModflowBoundary
    {
        return new static($boundaryId, $name, $geometry, $affectedLayers, $metadata);
    }

    protected function __construct(BoundaryId $boundaryId, BoundaryName $name, Geometry $geometry, AffectedLayers $affectedLayers, Metadata $metadata)
    {
        $this->boundaryId = $boundaryId;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->affectedLayers = $affectedLayers;
        $this->metadata = $metadata;
        $this->observationPoints = ObservationPointCollection::create();
    }

    public function updateName(BoundaryName $boundaryName): ModflowBoundary
    {
        $this->name = $boundaryName;
        return $this->self();
    }

    public function updateGeometry(Geometry $geometry): ModflowBoundary
    {
        $this->geometry = $geometry;
        return $this->self();
    }

    public function updateAffectedLayers(AffectedLayers $affectedLayers): ModflowBoundary
    {
        $this->affectedLayers = $affectedLayers;
        return $this->self();
    }

    public function updateMetadata(Metadata $metadata): ModflowBoundary
    {
        $this->metadata = $metadata;
        return $this->self();
    }

    public function addObservationPoint(ObservationPoint $point): ModflowBoundary
    {
        $this->observationPoints()->add($point);
        return $this->self();
    }

    public function getObservationPoint(ObservationPointId $id): ObservationPoint
    {
        return $this->observationPoints->get($id);
    }

    public function updateObservationPoint(ObservationPoint $point): ModflowBoundary
    {
        $this->observationPoints()->add($point);
        return $this->self();
    }

    public function affectedLayers(): AffectedLayers
    {
        return $this->affectedLayers;
    }

    public function boundaryId(): BoundaryId
    {
        return $this->boundaryId;
    }

    public function geometry(): Geometry
    {
        return $this->geometry;
    }

    public function name(): BoundaryName
    {
        return $this->name;
    }

    public function metadata(): Metadata
    {
        if (null === $this->metadata){
            $this->metadata = Metadata::create();
        }

        return $this->metadata;
    }

    public function observationPoints(): ObservationPointCollection
    {
        return $this->observationPoints;
    }

    public function dateTimeValues(ObservationPointId $observationPointId): DateTimeValuesCollection
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints[$observationPointId->toString()];
        return $observationPoint->dateTimeValues();
    }

    public function type(): BoundaryType
    {
        return BoundaryType::fromString($this::TYPE);
    }

    public function cardinality(): Cardinality
    {
        return Cardinality::fromString($this::CARDINALITY);
    }

    protected function hasObservationPoint(ObservationPointId $observationPointId): bool
    {
        return $this->observationPoints->has($observationPointId);
    }

    protected function addDateTimeValue(DateTimeValue $dateTimeValue, ObservationPointId $observationPointId)
    {
        $observationPoint = $this->observationPoints()->get($observationPointId);
        $observationPoint->addDateTimeValue($dateTimeValue);
        return $this;
    }
}
