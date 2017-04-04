<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class ActiveCells
{
    /**
     * Represents an 2D-Array of the active cells of a layer
     * @var array
     */
    private $layerData = [];

    /**
     * Represents the number of columns
     * @var int
     */
    private $nx;

    /**
     * Represents the number of rows
     * @var int
     */
    private $ny;

    /**
     * Represents the affected layers as a list of int
     * @var array
     */
    private $layers;

    public static function fromArrayAndGridSize(array $layerData, GridSize $gridSize): ActiveCells
    {
        return new self($layerData, [0], $gridSize);
    }

    public static function fromArrayGridSizeAndLayer(array $layerData, GridSize $gridSize, LayerNumber $layer): ActiveCells
    {
        return new self($layerData, [$layer->toInteger()], $gridSize);
    }

    public static function fromFullArray(array $arr): ActiveCells
    {
        $gridSize = GridSize::fromXY(count($arr), count($arr[0]));
        return new self($arr, [0], $gridSize);
    }

    public static function fromArray(array $arr): ActiveCells
    {
        $layerData = $arr['data'];
        $gridSize = GridSize::fromXY($arr['n_x'], $arr['n_y']);
        $layers = ($arr['layers']);
        return new self($layerData, $gridSize, $layers);
    }

    private function __construct(array $layerData, array $layers, GridSize $gridSize)
    {
        foreach ($layerData as $row => $cols){
            foreach ($cols as $col => $value){
                $layerData[intval($row)][intval($col)] = (bool)$value;
            }
        }

        $this->layerData = $layerData;
        $this->nx = $gridSize->nX();
        $this->ny = $gridSize->nY();
        $this->layers = $layers;
    }

    /**
     * This function returns the active cells in an element each cell
     * [
     *      [$layer, $row, $col],
     *      [$layer, $row, $col],
     *      [$layer, $row, $col],
     *      ...
     * ]
     * @return array
     *
     */
    public function cells(): array
    {
        $cells = [];
        foreach ($this->layers as $layer) {
            foreach ($this->layerData as $rowNumber => $row) {
                foreach ($row as $colNumber => $isActive) {
                    if ($isActive === 1 || $isActive === true) {
                        $cells[] = [$layer, $rowNumber, $colNumber];
                    }
                }
            }
        }

        return $cells;
    }

    public function layerData(): array
    {
        return $this->layerData;
    }

    public function layers(): array
    {
        return $this->layers;
    }

    public function fullArray(): array
    {
        $cells = [];
        for ($iR=0; $iR<$this->ny; $iR++){
            $cells[$iR] = [];
            for ($iC=0; $iC<$this->nx; $iC++) {
                $cells[$iR][$iC] = false;
            }
        }

        foreach ($this->layerData as $row => $cols){
            foreach ($cols as $col => $value){
                $cells[intval($row)][intval($col)] = $value;
            }
        }

        return $cells;
    }

    public function toArray(): array
    {
        return array(
            'data' => $this->layerData,
            'n_x' => $this->nx,
            'n_y' => $this->ny,
            'layers' => $this->layers
        );
    }
}
