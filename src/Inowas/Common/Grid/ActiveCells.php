<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class ActiveCells
{
    /** @var array  */
    private $cells = [];

    /** @var int  */
    private $nx;

    /** @var int  */
    private $ny;

    /** @var int */
    private $layer;

    public static function fromArrayAndGridSize(array $cells, GridSize $gridSize): ActiveCells
    {
        return new self($cells, $gridSize);
    }

    public static function fromArrayGridSizeAndLayer(array $cells, GridSize $gridSize, LayerNumber $layer): ActiveCells
    {
        return new self($cells, $gridSize, $layer);
    }

    public static function fromArray(array $arr): ActiveCells
    {
        $cells = $arr['cells'];
        $gridSize = GridSize::fromXY($arr['n_x'], $arr['n_y']);
        $layerNumber = LayerNumber::fromInteger($arr['layer']);
        return new self($cells, $gridSize, $layerNumber);
    }

    public static function fromObjectAndGridSize($obj, GridSize $gridSize): ActiveCells
    {
        $cells = array();
        foreach ($obj as $row => $cols){
            foreach ($cols as $col => $value){
                $cells[intval($row)][intval($col)] = $value;
            }
        }

        return new self($cells, $gridSize);
    }

    private function __construct(array $cells, GridSize $gridSize, ?LayerNumber $layerNumber = null)
    {
        foreach ($cells as $row => $cols){
            foreach ($cols as $col => $value){
                if ($value !== 0){$value = 1;}
                $cells[intval($row)][intval($col)] = $value;
            }
        }

        $this->cells = $cells;
        $this->nx = $gridSize->nX();
        $this->ny = $gridSize->nY();

        if (is_null($layerNumber)){
            $layerNumber = LayerNumber::fromInteger(0);
        }
        $this->layer = $layerNumber->toInteger();
    }

    public function cells(): array
    {
        return $this->cells;
    }

    public function fullArray(): array
    {
        $cells = [];
        for ($iR=0; $iR<$this->ny; $iR++){
            $cells[$iR] = [];
            for ($iC=0; $iC<$this->nx; $iC++) {
                $cells[$iR][$iC] = 0;
            }
        }

        foreach ($this->cells as $row => $cols){
            foreach ($cols as $col => $value){
                if ($value !== 0){$value = 1;}
                $cells[intval($row)][intval($col)] = $value;
            }
        }

        return $cells;
    }

    public function toArray(): array
    {
        return array(
            'cells' => $this->cells,
            'n_x' => $this->nx,
            'n_y' => $this->ny,
            'layer' => $this->layer
        );
    }

    public function layer(): LayerNumber
    {
        return LayerNumber::fromInteger($this->layer);
    }
}
