<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;

class BasPackageAdapter
{
    /**
     * @var ModFlowModel $model
     */
    protected $model;

    /**
     * BasPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model){
        $this->model = $model;
    }

    /**
     * @return Flopy2DArray
     */
    public function getIbound(): Flopy2DArray
    {
        if (! $this->model->getActiveCells() instanceof ActiveCells){
            return Flopy2DArray::fromValue(1, $this->model->getGridSize()->getNY(), $this->model->getGridSize()->getNY());
        }

        $activeCells = $this->model->getActiveCells()->toArray();

        $iBound = array();
        foreach ($activeCells as $nRow => $rowValue){
            $iBound[$nRow] = array();
            foreach ($rowValue as $nCol => $value){
                $iBound[$nRow][$nCol] = (int)$value;
            }
        }

        return Flopy2DArray::fromValue($iBound, $this->model->getGridSize()->getNY(), $this->model->getGridSize()->getNY());
    }

    /**
     * @return Flopy2DArray
     */
    public function getStrt(): Flopy2DArray
    {
        return Flopy2DArray::fromValue(1.0, $this->model->getGridSize()->getNY(), $this->model->getGridSize()->getNY());
    }

    /**
     * @return boolean
     */
    public function isIfrefm(): bool
    {
        return true;
    }

    /**
     * @return boolean
     */
    public function isIxsec(): bool
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isIchflg(): bool
    {
        return false;
    }

    /**
     * @return float|null
     */
    public function getStoper()
    {
        return null;
    }

    /**
     * @return float
     */
    public function getHnoflo(): float
    {
        return -999.99;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'bas';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 13;
    }
}