<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class RechargeBoundary extends AbstractBoundary
{
    const TYPE = 'rch';

    /** @var  ObservationPoint */
    protected $observationPoint;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param BoundaryName $name
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param BoundaryMetadata $metadata
     * @return RechargeBoundary
     */
    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        BoundaryMetadata $metadata
    ): RechargeBoundary
    {
        return new self($boundaryId, $name, $geometry, $affectedLayers, $metadata);
    }

    public function type(): BoundaryType
    {
        return BoundaryType::fromString($this::TYPE);
    }

    public function addRecharge(RechargeDateTimeValue $rechargeRate): ModflowBoundary
    {
        // In case of rechargeBoundary, the observationPointId is the boundaryId
        $observationPointId = ObservationPointId::fromString($this->boundaryId->toString());
        if (! $this->hasOp($observationPointId)) {
            $this->addOrUpdateOp($this->createObservationPoint());
        }

        $this->addDateTimeValue($rechargeRate, $observationPointId);
        return $this->self();
    }

    private function createObservationPoint(): ObservationPoint
    {
        return ObservationPoint::fromIdTypeNameAndGeometry(
            ObservationPointId::fromString($this->boundaryId->toString()),
            $this->type(),
            ObservationPointName::fromString($this->name->toString()),
            $this->geometry->getPointFromPolygon()
        );
    }

    public function findValueByDateTime(\DateTimeImmutable $dateTime): RechargeDateTimeValue
    {
        /** @var ObservationPoint $op */
        $op = $this->getOp(ObservationPointId::fromString($this->boundaryId->toString()));
        $value = $op->findValueByDateTime($dateTime);

        if ($value instanceof RechargeDateTimeValue){
            return $value;
        }

        return RechargeDateTimeValue::fromParams($dateTime, 0);
    }

    protected function self(): ModflowBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->affectedLayers, $this->metadata);
        $self->activeCells = $this->activeCells;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }
}
