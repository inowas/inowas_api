<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

class IBound implements \JsonSerializable
{
    const ACTIVE = 1;
    const INACTIVE = 0;

    private $nx;
    private $ny;
    private $nz;
    private $value;

    /**
     * @param ActiveCells $activeCells
     * @param $nLay
     * @param $nRow
     * @param $nCol
     * @return IBound
     */
    public static function fromActiveCells(ActiveCells $activeCells, int $nLay, int $nRow, int $nCol)
    {
        $instance = new self();

        // Generate an IBound-Array with all cells inactive
        $iBound = array();
        for ($iLay = 0; $iLay<$nLay; $iLay++){
            $iBound[$iLay] = array();
            for ($iRow = 0; $iRow<$nRow; $iRow++){
                $iBound[$iLay][$iRow] = array_fill(0, $nCol, $instance::INACTIVE);
            }
        }

        $activeCells = $activeCells->toArray();
        for ($iLay = 0; $iLay<$nLay; $iLay++){
            foreach ($activeCells as $iRow => $rowValue){
                foreach ($rowValue as $iCol => $value){
                    $iBound[$iLay][$iRow][$iCol] = (int)$value;
                }
            }
        }

        $instance->nx = $nCol;
        $instance->ny = $nRow;
        $instance->nz = $nLay;
        $instance->value = $iBound;
        return $instance;
    }

    /**
     * @param int $value
     * @param int $nLay
     * @param int $nRow
     * @param int $nCol
     * @return IBound
     */
    public static function fromValue(int $value, int $nLay, int $nRow, int $nCol){
        $instance = new self();

        // Generate an IBound-Array with all cells inactive
        $iBound = array();
        for ($iLay = 0; $iLay<$nLay; $iLay++){
            $iBound[$iLay] = array();
            for ($iRow = 0; $iRow<$nRow; $iRow++){
                $iBound[$iLay][$iRow] = array_fill(0, $nCol, $value);
            }
        }

        $instance->nx = $nCol;
        $instance->ny = $nRow;
        $instance->nz = $nLay;
        $instance->value = $iBound;
        return $instance;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->value;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return mixed
     */
    public function getNx()
    {
        return $this->nx;
    }

    /**
     * @return mixed
     */
    public function getNy()
    {
        return $this->ny;
    }

    /**
     * @return mixed
     */
    public function getNz()
    {
        return $this->nz;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
