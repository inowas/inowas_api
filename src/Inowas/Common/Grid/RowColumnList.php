<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class RowColumnList
{
    /**
     * Represents a list of cells with layRowCol-values
     * [
     *  [row,col],
     *  [row,col],
     *  [row,col],
     *  [row,col]
     * ]
     *
     * @var array
     */
    private $cells;


    public static function fromArray(array $cells): RowColumnList
    {
        return new self($cells);
    }

    public static function fromAffectedCells(AffectedCells $affectedCells):  RowColumnList
    {
        return $affectedCells->rowColumnList();
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
