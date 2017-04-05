<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;

class RiverBoundary extends AbstractBoundary
{

    const TYPE = 'riv';

    public static function create(BoundaryId $boundaryId): RiverBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithIdNameAndGeometry(
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry
    ): RiverBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        return $self;
    }

    public function setActiveCells(ActiveCells $activeCells): RiverBoundary
    {
        return new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return self::TYPE;
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function dataToJson(): string
    {
        return json_encode([]);
    }
}
