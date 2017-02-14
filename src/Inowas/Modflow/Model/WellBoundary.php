<?php

namespace Inowas\Modflow\Model;

class WellBoundary extends AbstractModflowBoundary
{

    /** @var  LayerNumber */
    protected $layerNumber;

    /** @var  PumpingRate */
    protected $pumpingRate;

    /** @var  WellType */
    protected $wellType;

    public static function create(BoundaryId $boundaryId): WellBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithAllParams(
        BoundaryId $boundaryId,
        BoundaryName $name,
        BoundaryGeometry $geometry,
        WellType $wellType,
        LayerNumber $layerNumber,
        PumpingRate $pumpingRate
    ): WellBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        $self->layerNumber = $layerNumber;
        $self->wellType = $wellType;
        $self->pumpingRate = $pumpingRate;
        return $self;
    }

    public function layerNumber(): LayerNumber
    {
        return $this->layerNumber;
    }

    public function pumpingRate(): PumpingRate
    {
        return $this->pumpingRate;
    }

    public function wellType(): WellType
    {
        return $this->wellType;
    }
}
