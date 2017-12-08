<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class LayerRowColumnList
{
    /**
     * Represents a list of cells with layRowCol-values
     * [
     *  [lay,row,col],
     *  [lay,row,col],
     *  [lay,row,col],
     *  [lay,row,col]
     * ]
     *
     * @var array
     */
    private $cells;


    public static function fromArray(array $cells): LayerRowColumnList
    {
        return new self($cells);
    }

    public static function fromActiveCells(ActiveCells $activeCells):  LayerRowColumnList
    {
        return new self($activeCells->cells());
    }

    public static function fromAffectedCellsAndAffectedLayers(AffectedCells $affectedCells, AffectedLayers $affectedLayers):  LayerRowColumnList
    {
        return new self($affectedCells->layerRowColumns($affectedLayers));
    }

    private function __construct(array $cells)
    {
        $this->cells = $cells;
    }

    public function cells(): array
    {
        return $this->cells;
    }


    public function toArray(): array
    {
        return $this->cells;
    }

    public function count(): int
    {
        return \count($this->cells());
    }
}
