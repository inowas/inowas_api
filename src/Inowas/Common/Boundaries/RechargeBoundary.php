<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;

class RechargeBoundary extends AbstractBoundary
{
    public static function create(BoundaryId $boundaryId): RechargeBoundary
    {
        return new self($boundaryId);
    }

    public function setActiveCells(ActiveCells $activeCells): RechargeBoundary
    {
        return new self($this->boundaryId, $this->name, $this->geometry, $activeCells);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return "rch";
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
