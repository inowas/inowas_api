<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Point;
use Inowas\Common\Id\ObservationPointId;

class ObservationPoint implements \JsonSerializable
{

    /** @var  ObservationPointId */
    protected $id;

    /** @var  Point */
    protected $geometry;

    /** @var  ObservationPointName */
    protected $name;

    /** @var  BoundaryType */
    protected $type;

    /** @var  array */
    protected $dateTimeValues = [];

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ObservationPointId $id
     * @param BoundaryType $type
     * @param ObservationPointName $name
     * @param Point $geometry
     * @return ObservationPoint
     */
    public static function fromIdTypeNameAndGeometry(ObservationPointId $id, BoundaryType $type, ObservationPointName $name, Point $geometry): ObservationPoint
    {
        return new self($id, $type, $name, $geometry);
    }

    private function __construct(ObservationPointId $id, BoundaryType $type, ObservationPointName $name, Point $geometry)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->type = $type;
    }

    public function addDateTimeValue(DateTimeValue $dateTimeValue): ObservationPoint
    {
        $this->dateTimeValues[] = $dateTimeValue;
        $self = new self($this->id, $this->type, $this->name, $this->geometry);
        $self->dateTimeValues = $this->dateTimeValues;
        return $self;
    }

    public function id(): ObservationPointId
    {
        return $this->id;
    }

    public function type(): BoundaryType
    {
        return $this->type;
    }

    public function geometry(): Point
    {
        return $this->geometry;
    }

    public function name(): ObservationPointName
    {
        return $this->name;
    }

    public function dateTimeValues(): array
    {
        return $this->dateTimeValues;
    }

    public function dateTimeValuesDescription(): array
    {
        if (count($this->dateTimeValues()) > 0) {
            /** @var DateTimeValue $dateTimeValue */
            $dateTimeValue = $this->dateTimeValues[0];
            return $dateTimeValue->valuesDescription();
        }

        return [];
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry->toArray(),
            'date_time_values' => $this->dateTimeValues
        );
    }

    public function jsonSerialize(): array
    {
        $valuesDescription = [];
        if (count($this->dateTimeValues()) > 0) {
            /** @var DateTimeValue $dateTimeValue */
            $dateTimeValue = $this->dateTimeValues[0];
            $valuesDescription = $dateTimeValue->valuesDescription();
        }

        return array(
            'id' => $this->id->toString(),
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry->toArray(),
            'values_description' => $valuesDescription,
            'values' => $this->dateTimeValues
        );
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): ?DateTimeValue
    {

        $values = $this->dateTimeValues();
        usort($values, function ($v1, $v2) {

            /** @var $v1 DateTimeValue */
            $dtV1 = $v1->dateTime();

            /** @var $v2 DateTimeValue */
            $dtV2 = $v2->dateTime();

            return ($dtV1 < $dtV2) ? +1 : -1;
        });

        /** @var DateTimeValue $value */
        foreach ($values as $value) {
            if ($dateTime >= $value->dateTime()){
                return $value;
            }
        }

        return null;
    }
}
