<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\LayerNumber;

class WellBoundary extends AbstractBoundary
{

    /** @var  LayerNumber */
    protected $layerNumber;

    /** @var  PumpingRates */
    protected $pumpingRates;

    /** @var  WellType */
    protected $wellType;

    public static function create(\Inowas\Common\Id\BoundaryId $boundaryId): WellBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithAllParams(
        \Inowas\Common\Id\BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        WellType $wellType,
        LayerNumber $layerNumber,
        PumpingRates $pumpingRates
    ): WellBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        $self->layerNumber = $layerNumber;
        $self->wellType = $wellType;
        $self->pumpingRates = $pumpingRates;
        return $self;
    }

    public function type(): string
    {
        return 'well';
    }

    public function layerNumber(): LayerNumber
    {
        return $this->layerNumber;
    }

    public function pumpingRates(): PumpingRates
    {
        return $this->pumpingRates;
    }

    public function wellType(): WellType
    {
        return $this->wellType;
    }

    public function metadata(): array
    {
        return ['well_type' => $this->wellType->type()];
    }

    public function dataToJson(): string
    {
        return json_encode($this->pumpingRates);
    }
}