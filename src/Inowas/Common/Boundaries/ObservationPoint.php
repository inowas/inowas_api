<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Point;
use Inowas\Common\Modflow\Name;

class ObservationPoint implements \JsonSerializable
{
    /** @var  Point */
    protected $geometry;

    /** @var  Name */
    protected $name;

    /** @var  BoundaryType */
    protected $type;

    /** @var  DateTimeValuesCollection */
    protected $dateTimeValues;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryType $type
     * @param Name $name
     * @param Point $geometry
     * @return ObservationPoint
     */
    public static function fromTypeNameAndGeometry(BoundaryType $type, Name $name, Point $geometry): ObservationPoint
    {
        $self = new self($type, $name, $geometry);
        $self->dateTimeValues = DateTimeValuesCollection::create();
        return $self;
    }

    public static function fromArray(array $arr): ObservationPoint
    {
        $type = BoundaryType::fromString($arr['type']);
        $self = new self(
            $type,
            Name::fromString($arr['name']),
            new Point($arr['geometry'][0], $arr['geometry'][1])
        );

        $self->dateTimeValues = DateTimeValuesCollection::fromTypeAndArray($type, $arr['date_time_values']);
        return $self;
    }

    private function __construct(BoundaryType $type, Name $name, Point $geometry)
    {
        $this->name = $name;
        $this->geometry = $geometry;
        $this->type = $type;
    }

    public function addDateTimeValue(DateTimeValue $dateTimeValue): ObservationPoint
    {
        $this->dateTimeValues->add($dateTimeValue);
        $self = new self($this->type, $this->name, $this->geometry);
        $self->dateTimeValues = $this->dateTimeValues;
        return $self;
    }

    public function type(): BoundaryType
    {
        return $this->type;
    }

    public function geometry(): Point
    {
        return $this->geometry;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function getDateTimes(): array
    {
        return $this->dateTimeValues()->getDateTimes();
    }

    public function dateTimeValues(): DateTimeValuesCollection
    {
        return $this->dateTimeValues;
    }

    public function toArray(): array
    {
        return array(
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry->toArray(),
            'type' => $this->type->toString(),
            'date_time_values' => $this->dateTimeValues->toArray()
        );
    }

    public function jsonSerialize(): array
    {
        return array(
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry->toArray(),
            'values' => $this->dateTimeValues
        );
    }

    public function findValueByDateTime(DateTime $dateTime): ?DateTimeValue
    {
        return $this->dateTimeValues()->findValueByDateTime($dateTime);
    }
}
