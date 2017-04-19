<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\ObservationPointId;

class ObservationPoint implements \JsonSerializable
{

    /** @var  ObservationPointId */
    protected $id;

    /** @var  Geometry */
    protected $geometry;

    /** @var  ObservationPointName */
    protected $name;

    /** @var  array */
    protected $dateTimeValues = [];

    public static function fromIdNameAndGeometry(ObservationPointId $id, ObservationPointName $name, ?Geometry $geometry = null): ObservationPoint
    {
        return new self($id, $name, $geometry);
    }

    private function __construct(ObservationPointId $id, ObservationPointName $name, ?Geometry $geometry = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geometry = $geometry;
    }

    public function addDateTimeValue(DateTimeValue $dateTimeValue): ObservationPoint
    {
        $this->dateTimeValues[] = $dateTimeValue;
        $self = new self($this->id, $this->name, $this->geometry);
        $self->dateTimeValues = $this->dateTimeValues;
        return $self;
    }

    public function id(): ObservationPointId
    {
        return $this->id;
    }

    public function geometry(): ?Geometry
    {
        return $this->geometry;
    }

    public function geometryArray(): ?array
    {
        return ($this->geometry instanceof Geometry) ? $this->geometry->toArray() : null;
    }

    public function geometryJson(): string
    {
        return ($this->geometry instanceof Geometry) ? $this->geometry->toJson() : "{}";
    }

    public function name(): ObservationPointName
    {
        return $this->name;
    }

    public function dateTimeValues(): array
    {
        return $this->dateTimeValues;
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'name' => $this->name()->toString(),
            'geometry' => $this->geometryArray(),
            'date_time_values' => $this->dateTimeValues
        );
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): ?DateTimeValue
    {

        $values = $this->dateTimeValues();
        usort($values, function ($v1, $v2) {

            /** @var $v1 WellDateTimeValue */
            $dtV1 = $v1->dateTime();

            /** @var $v2 WellDateTimeValue */
            $dtV2 = $v2->dateTime();

            return ($dtV1 < $dtV2) ? +1 : -1;
        });

        /** @var WellDateTimeValue $value */
        foreach ($values as $value) {
            if ($dateTime > $value->dateTime()){
                return $value;
            }
        }

        return null;
    }
}
