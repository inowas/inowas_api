<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\Name;

class ObservationPoint implements \JsonSerializable
{
    /** @var  ObservationPointId */
    protected $id;

    /** @var  Point */
    protected $geometry;

    /** @var  Name */
    protected $name;

    /** @var  BoundaryType */
    protected $type;

    /** @var  DateTimeValuesCollection */
    protected $dateTimeValuesCollection;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ObservationPointId $id
     * @param BoundaryType $type
     * @param Name $name
     * @param Point $geometry
     * @return ObservationPoint
     */
    public static function fromIdTypeNameAndGeometry(ObservationPointId $id, BoundaryType $type, Name $name, Point $geometry): ObservationPoint
    {
        $self = new self($id, $type, $name, $geometry);
        $self->dateTimeValuesCollection = DateTimeValuesCollection::create();
        return $self;
    }

    /**
     * @param array $arr
     * @param BoundaryType $type
     * @return ObservationPoint
     * @throws \Inowas\Common\Exception\KeyHasUseException
     */
    public static function fromArray(array $arr, BoundaryType $type): ObservationPoint
    {
        $self = new self(
            ObservationPointId::fromString($arr['id']),
            $type,
            Name::fromString($arr['name']),
            Geometry::fromArray($arr['geometry'])->getPoint()
        );

        $self->dateTimeValuesCollection = DateTimeValuesCollection::fromTypeAndArray($type, $arr['date_time_values']);
        return $self;
    }

    /**
     * ObservationPoint constructor.
     * @param ObservationPointId $id
     * @param BoundaryType $type
     * @param Name $name
     * @param Point $geometry
     */
    private function __construct(ObservationPointId $id, BoundaryType $type, Name $name, Point $geometry)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->type = $type;
    }

    /**
     * @param DateTimeValue $dateTimeValue
     * @return ObservationPoint
     * @throws \Inowas\Common\Exception\KeyHasUseException
     */
    public function addDateTimeValue(DateTimeValue $dateTimeValue): ObservationPoint
    {
        $this->dateTimeValuesCollection->add($dateTimeValue);
        $self = new self($this->id, $this->type, $this->name, $this->geometry);
        $self->dateTimeValuesCollection = $this->dateTimeValuesCollection;
        return $self;
    }

    /**
     * @return BoundaryType
     */
    public function type(): BoundaryType
    {
        return $this->type;
    }

    /**
     * @return Point
     */
    public function geometry(): Point
    {
        return $this->geometry;
    }

    /**
     * @return Name
     */
    public function name(): Name
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getDateTimes(): array
    {
        return $this->dateTimeValues()->getDateTimes();
    }

    /**
     * @return DateTimeValuesCollection
     */
    public function dateTimeValues(): DateTimeValuesCollection
    {
        return $this->dateTimeValuesCollection;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'name' => $this->name()->toString(),
            'geometry' => Geometry::fromPoint($this->geometry)->toArray(),
            'type' => $this->type->toString(),
            'date_time_values' => $this->dateTimeValuesCollection->toArray()
        );
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return array(
            'id' => $this->id->toString(),
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry->toArray(),
            'values' => $this->dateTimeValuesCollection
        );
    }

    /**
     * @param DateTime $dateTime
     * @return DateTimeValue|null
     */
    public function findValueByDateTime(DateTime $dateTime): ?DateTimeValue
    {
        return $this->dateTimeValues()->findValueByDateTime($dateTime);
    }

    /**
     * @return ObservationPointId
     */
    public function id(): ObservationPointId
    {
        return $this->id;
    }
}
