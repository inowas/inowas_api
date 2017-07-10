<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\ObservationPointNotFoundInBoundaryException;
use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ObservationPointId;

class GeneralHeadBoundary extends AbstractBoundary
{
    const CARDINALITY = 'n';
    const TYPE = 'ghb';

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $boundaryId
     * @param BoundaryName $name
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param BoundaryMetadata $metadata
     * @return GeneralHeadBoundary
     */
    public static function createWithParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        BoundaryMetadata $metadata
    ): GeneralHeadBoundary
    {
        return new self($boundaryId, $name, $geometry, $affectedLayers, $metadata);
    }

    public function type(): BoundaryType
    {
        return BoundaryType::fromString($this::TYPE);
    }

    public function addGeneralHeadValueToObservationPoint(ObservationPointId $observationPointId, GeneralHeadDateTimeValue $ghbDateTimeValue): ModflowBoundary
    {
        if (! $this->hasObservationPoint($observationPointId)){
            throw ObservationPointNotFoundInBoundaryException::withIds($this->boundaryId, $observationPointId);
        }

        $this->addDateTimeValue($ghbDateTimeValue, $observationPointId);
        return $this->self();
    }

    protected function self(): ModflowBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $this->affectedLayers, $this->metadata);
        $self->activeCells = $this->activeCells;
        $self->observationPoints = $this->observationPoints;
        return $self;
    }
}
