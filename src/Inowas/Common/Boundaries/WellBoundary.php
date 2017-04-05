<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Id\BoundaryId;

class WellBoundary extends AbstractBoundary
{

    const TYPE = 'well';

    /** @var  LayerNumber */
    protected $layerNumber;

    /** @var  PumpingRates */
    protected $pumpingRates;

    /** @var  WellType */
    protected $wellType;

    public static function create(BoundaryId $boundaryId): WellBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithAllParams(
        BoundaryId $boundaryId,
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

    public function setActiveCells(ActiveCells $activeCells): WellBoundary
    {
        $self = new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
        $self->layerNumber = $this->layerNumber;
        $self->wellType = $this->wellType;
        $self->pumpingRates = $this->pumpingRates;
        return $self;
    }

    public function type(): string
    {
        return self::TYPE;
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
        return [
            'well_type' => $this->wellType->type(),
            'layer' => $this->layerNumber->toInteger()
        ];
    }

    public function dataToJson(): string
    {
        return json_encode($this->pumpingRates);
    }
}
