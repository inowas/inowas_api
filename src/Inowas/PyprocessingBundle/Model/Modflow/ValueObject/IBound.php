<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use AppBundle\Model\ActiveCells;

class IBound extends Flopy3DArray
{

    const ACTIVE = 1;
    const INACTIVE = 0;

    /**
     * @param ActiveCells $activeCells
     * @param $nLay
     * @param $nRow
     * @param $nCol
     * @return IBound
     */
    public static function fromActiveCells(ActiveCells $activeCells, $nLay, $nRow, $nCol)
    {

        $instance = new self();

        // Generate an IBound-Array with all Cells Active
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
     * @return array
     */
    public function toArray()
    {
        return $this->value;
    }
}