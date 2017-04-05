<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Grid\ActiveCells;
use Inowas\Common\Id\BoundaryId;

class GeneralHeadHeadBoundary extends AbstractBoundary
{
    const TYPE = 'ghb';

    public static function create(BoundaryId $boundaryId): GeneralHeadHeadBoundary
    {
        return new self($boundaryId);
    }

    public function setActiveCells(ActiveCells $activeCells): GeneralHeadHeadBoundary
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

    public function dataToJson(): string
    {
        return json_encode([]);
    }
}
