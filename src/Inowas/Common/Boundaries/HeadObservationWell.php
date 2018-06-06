<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedCells;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;
use Inowas\Common\Modflow\Name;

class HeadObservationWell extends ModflowBoundary
{
    /**
     *
     */
    public const CARDINALITY = '1';
    /**
     *
     */
    public const TYPE = 'hob';

    /**
     * @param array $arr
     * @return ModflowBoundary
     * @throws \Exception
     */
    public static function fromArray(array $arr): ModflowBoundary
    {
        $affectedCells = AffectedCells::create();
        if (array_key_exists('active_cells', $arr)) {
            $affectedCells = AffectedCells::fromArray($arr['active_cells']);
        }

        $self = new self(
            BoundaryId::fromString($arr['id']),
            Name::fromString($arr['name']),
            Geometry::fromArray($arr['geometry']),
            $affectedCells,
            AffectedLayers::fromArray($arr['affected_layers']),
            Metadata::fromArray((array)$arr['metadata'])
        );

        /** @var array $dateTimeValues */
        $dateTimeValues = $arr['date_time_values'];
        foreach ($dateTimeValues as $date_time_value) {
            $self->addHeadObservation(
                HeadObservationWellDateTimeValue::fromParams(
                    DateTime::fromAtom($date_time_value['date_time']),
                    $date_time_value['values'][0]
                )
            );
        }

        return $self;
    }

    /**
     * @param HeadObservationWellDateTimeValue $head
     * @return HeadObservationWell
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Inowas\Common\Exception\KeyInvalidException
     * @throws \Inowas\Common\Exception\KeyHasUseException
     */
    public function addHeadObservation(HeadObservationWellDateTimeValue $head): HeadObservationWell
    {
        $observationPointId = ObservationPointId::fromString('OP');
        if (! $this->hasObservationPoint($observationPointId)) {
            $this->addObservationPoint(
                ObservationPoint::fromIdTypeNameAndGeometry(
                    $observationPointId,
                    $this->type(),
                    Name::fromString($this->name->toString()),
                    $this->geometry->getPointFromGeometry()
                )
            );
        }

        $this->addDateTimeValue($head, $observationPointId);
        return $this->self();
    }

    /**
     * @param DateTime $dateTime
     * @return HeadObservationWellDateTimeValue|null
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function findValueByDateTime(DateTime $dateTime): ?HeadObservationWellDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getObservationPoint(ObservationPointId::fromString('OP'));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof HeadObservationWellDateTimeValue){
            return $value;
        }

        return null;
    }

    /**
     * @return array
     * @throws \Inowas\Common\Exception\InvalidTypeException
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function toArray(): array
    {
        return array(
            'id' => $this->boundaryId()->toString(),
            'type' => $this->type()->toString(),
            'name' => $this->name()->toString(),
            'geometry' => $this->geometry()->toArray(),
            'active_cells' => $this->affectedCells()->toArray(),
            'affected_layers' => $this->affectedLayers()->toArray(),
            'metadata' => $this->metadata()->toArray(),
            'date_time_values' => $this->getObservationPoint(ObservationPointId::fromString('OP'))->dateTimeValues()->toArray()
        );
    }
}
