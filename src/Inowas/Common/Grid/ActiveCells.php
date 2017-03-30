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

    public static function fromArrayAndGridSize(array $cells, GridSize $gridSize)
    {
        return new self($cells, $gridSize);
    }

    public static function fromArray(array $arr){
        $cells = $arr['cells'];
        $gridSize = GridSize::fromXY($arr['n_x'], $arr['n_y']);
        return new self($cells, $gridSize);
    }

    private function __construct(array $cells, GridSize $gridSize)
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
    }

    public static function fromObjectAndGridSize($obj, GridSize $gridSize)
    {
        $cells = array();
        foreach ($obj as $row => $cols){
            foreach ($cols as $col => $value){
                $cells[intval($row)][intval($col)] = $value;
            }
        }

        return new self($cells, $gridSize);
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
            'n_y' => $this->ny
        );
    }
}
