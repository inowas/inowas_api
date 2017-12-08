<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

/**
 * Represents cells with values
 */
class AffectedCells
{
    /**
     * @var array
     * Structure:
     * [
     *  [x, y, value (optional, default: 1)]
     * ]
     */
    private $cells;

    public static function create(): AffectedCells
    {
        return self::fromCells([]);
    }

    public static function fromArray(array $cells): AffectedCells
    {
        return self::fromCells($cells);
    }

    public static function fromCells(array $cells): AffectedCells
    {
        return new self($cells);
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
        return $this->cells();
    }

    public function activeCells(GridSize $gridSize, AffectedLayers $layers): ActiveCells
    {
        $layerData = [];
        for ($y = 0; $y < $gridSize->nY(); $y++) {
            $layerData[$y] = [];
            for ($x = 0; $x < $gridSize->nY(); $x++) {
                $layerData[$y][$x] = false;
            }
        }

        foreach ($this->cells as $cell) {
            $layerData[$cell[1]][$cell[0]] = true;
            if (\count($cell) === 3) {
                $layerData[$cell[1]][$cell[0]] = $cell[3];
            }
        }
        return ActiveCells::fromArrayGridSizeAndLayer($layerData, $gridSize, $layers);
    }

    public function rowColumnList(): RowColumnList
    {
        $data = [];
        foreach ($this->cells as $cell) {
            $data[] = [$cell[1], $cell[0]];
        }

        return RowColumnList::fromArray($data);
    }

    public function layerRowColumns(AffectedLayers $affectedLayers): LayerRowColumnList
    {
        $data = [];
        foreach ($affectedLayers->toArray() as $layer) {
            foreach ($this->cells as $cell) {
                $data[] = [$layer, $cell[1], $cell[0]];
            }
        }

        return LayerRowColumnList::fromArray($data);
    }

    public function isEmpty(): bool
    {
        return \count($this->cells()) === 0;
    }
}
