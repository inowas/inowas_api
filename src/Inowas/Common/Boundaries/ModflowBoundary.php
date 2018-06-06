<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\Name;

class ModflowBoundary
{
    /**
     *
     */
    public CONST CARDINALITY = '';
    /**
     *
     */
    public CONST TYPE = '';

    /** @var  BoundaryId */
    protected $id;

    /** @var  Name */
    protected $name;

    /** @var  Geometry */
    protected $geometry;

    /** @var  AffectedCells */
    protected $affectedCells;

    /** @var  AffectedLayers */
    protected $affectedLayers;

    /** @var  Metadata */
    protected $metadata;

    /** @var ObservationPointCollection  */
    protected $observationPoints;

    /**
     * @return ModflowBoundary
     */
    protected function self(): ModflowBoundary
    {
        $self = new static($this->id, $this->name, $this->geometry, $this->affectedCells, $this->affectedLayers, $this->metadata);
        $self->id = $this->id;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param Name $name
     * @param Geometry $geometry
     * @param AffectedCells $affectedCells
     * @param AffectedLayers $affectedLayers
     * @param Metadata $metadata
     * @return ModflowBoundary
     */
    public static function createWithParams(
        Name $name,
        Geometry $geometry,
        AffectedCells $affectedCells,
        AffectedLayers $affectedLayers,
        Metadata $metadata
    ): ModflowBoundary
    {
        return new static(BoundaryId::fromString($name->slugified()), $name, $geometry, $affectedCells, $affectedLayers, $metadata);
    }

    /**
     * @param array $arr
     * @return ModflowBoundary
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    public static function fromArray(array $arr): ModflowBoundary
    {
        $affectedCells = AffectedCells::create();
        if (array_key_exists('active_cells', $arr)) {
            $affectedCells = AffectedCells::fromArray($arr['active_cells']);
        }

        $static = new static(
            BoundaryId::fromString($arr['id']),
            Name::fromString($arr['name']),
            Geometry::fromArray($arr['geometry']),
            $affectedCells,
            AffectedLayers::fromArray($arr['affected_layers']),
            Metadata::fromArray($arr['metadata'])
        );

        $static->id = BoundaryId::fromString($arr['id']);
        $static->observationPoints = ObservationPointCollection::fromArray($arr['observation_points'], BoundaryType::fromString(static::TYPE));
        return $static;
    }

    /**
     * ModflowBoundary constructor.
     * @param BoundaryId $id
     * @param Name $name
     * @param Geometry $geometry
     * @param AffectedCells $affectedCells
     * @param AffectedLayers $affectedLayers
     * @param Metadata $metadata
     */
    protected function __construct(BoundaryId $id, Name $name, Geometry $geometry, AffectedCells $affectedCells, AffectedLayers $affectedLayers, Metadata $metadata)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->affectedCells = $affectedCells;
        $this->affectedLayers = $affectedLayers;
        $this->metadata = $metadata;
        $this->observationPoints = ObservationPointCollection::create();
    }

    /**
     * @param Name $boundaryName
     * @return ModflowBoundary
     */
    public function updateName(Name $boundaryName): ModflowBoundary
    {
        $this->name = $boundaryName;
        return $this->self();
    }

    /**
     * @param Geometry $geometry
     * @return ModflowBoundary
     */
    public function updateGeometry(Geometry $geometry): ModflowBoundary
    {
        $this->geometry = $geometry;
        return $this->self();
    }

    /**
     * @param AffectedCells $affectedCells
     * @return ModflowBoundary
     */
    public function updateAffectedCells(AffectedCells $affectedCells): ModflowBoundary
    {
        $this->affectedCells = $affectedCells;
        return $this->self();
    }

    /**
     * @param AffectedLayers $affectedLayers
     * @return ModflowBoundary
     */
    public function updateAffectedLayers(AffectedLayers $affectedLayers): ModflowBoundary
    {
        $this->affectedLayers = $affectedLayers;
        return $this->self();
    }

    /**
     * @param Metadata $metadata
     * @return ModflowBoundary
     */
    public function updateMetadata(Metadata $metadata): ModflowBoundary
    {
        $this->metadata = $metadata;
        return $this->self();
    }

    /**
     * @param ObservationPoint $point
     * @return ModflowBoundary
     */
    public function addObservationPoint(ObservationPoint $point): ModflowBoundary
    {
        $this->observationPoints()->add($point);
        return $this->self();
    }

    /**
     * @param ObservationPointId $id
     * @return ObservationPoint
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function getObservationPoint(ObservationPointId $id): ObservationPoint
    {
        return $this->observationPoints->get($id);
    }

    /**
     * @param ObservationPoint $point
     * @return ModflowBoundary
     */
    public function updateObservationPoint(ObservationPoint $point): ModflowBoundary
    {
        $this->observationPoints()->add($point);
        return $this->self();
    }

    /**
     * @return AffectedCells
     */
    public function affectedCells(): AffectedCells
    {
        return $this->affectedCells;
    }

    /**
     * @return AffectedLayers
     */
    public function affectedLayers(): AffectedLayers
    {
        return $this->affectedLayers;
    }

    /**
     * @return Geometry
     */
    public function geometry(): Geometry
    {
        return $this->geometry;
    }

    /**
     * @return BoundaryId
     */
    public function boundaryId(): BoundaryId
    {
        return $this->id;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return Metadata
     */
    public function metadata(): Metadata
    {
        if (null === $this->metadata){
            $this->metadata = Metadata::create();
        }

        return $this->metadata;
    }

    /**
     * @return ObservationPointCollection
     */
    public function observationPoints(): ObservationPointCollection
    {
        return $this->observationPoints;
    }

    /**
     * @param ObservationPointId $id
     * @return DateTimeValuesCollection
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function dateTimeValues(ObservationPointId $id): DateTimeValuesCollection
    {
        /** @var ObservationPoint $observationPoint */
        $observationPoint = $this->observationPoints->get($id);
        return $observationPoint->dateTimeValues();
    }

    /**
     * @return array
     */
    public function getDateTimes(): array
    {
        return $this->observationPoints()->getDateTimes();
    }

    /**
     * @return BoundaryType
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    public function type(): BoundaryType
    {
        return BoundaryType::fromString($this::TYPE);
    }

    /**
     * @return Cardinality
     */
    public function cardinality(): Cardinality
    {
        return Cardinality::fromString($this::CARDINALITY);
    }

    /**
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     */
    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'type' => $this->type()->toString(),
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry()->toArray(),
            'active_cells' => $this->affectedCells()->toArray(),
            'affected_layers' => $this->affectedLayers()->toArray(),
            'metadata' => $this->metadata()->toArray(),
            'observation_points' => $this->observationPoints()->toArray(),
        );
    }

    /**
     * @param ObservationPointId $id
     * @return bool
     */
    protected function hasObservationPoint(ObservationPointId $id): bool
    {
        return $this->observationPoints->has($id);
    }

    /**
     * @param DateTimeValue $dateTimeValue
     * @param ObservationPointId $id
     * @return $this
     * @throws \Inowas\Common\Exception\KeyInvalidException
     * @throws \Inowas\Common\Exception\KeyHasUseException
     */
    protected function addDateTimeValue(DateTimeValue $dateTimeValue, ObservationPointId $id): self
    {
        $observationPoint = $this->observationPoints()->get($id);
        $observationPoint->addDateTimeValue($dateTimeValue);
        return $this;
    }
}
