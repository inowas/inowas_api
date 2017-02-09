<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class ModflowModelArea
{
    /** @var ModflowModelActiveCells */
    private $activeCells;

    /** @var Polygon */
    private $geometry;

    public static function fromPolygon(Polygon $polygon): ModflowModelArea
    {
        return new self($polygon, null);
    }

    public static function fromPolygonAndActiveCells(Polygon $polygon, ModflowModelActiveCells $activeCells = null): ModflowModelArea
    {
        return new self($polygon, $activeCells);
    }

    private function __construct(Polygon $polygon, ModflowModelActiveCells $modflowModelActiveCells)
    {
        $this->geometry = $polygon;
        $this->activeCells = $modflowModelActiveCells;
    }

    public function setActiveCells(ModflowModelActiveCells $activeCells): ModflowModelArea
    {
        return new self($this->geometry, $activeCells);
    }

    public function geometry(): Polygon
    {
        return $this->geometry;
    }

    public function activeCells(): ModflowModelActiveCells
    {
        return $this->activeCells;
    }
}
