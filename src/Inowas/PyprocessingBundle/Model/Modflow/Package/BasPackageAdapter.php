<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\ActiveCells;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

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
     * @return Flopy3DArray
     */
    public function getIbound(): Flopy3DArray
    {
        if (! $this->model->getActiveCells() instanceof ActiveCells){
            return Flopy3DArray::fromValue(1, $this->model->getGridSize()->getNY(), $this->model->getGridSize()->getNY());
        }

        $activeCells = $this->model->getActiveCells()->toArray();

        $iBound = array();
        for ($nLay = 0; $nLay<$this->model->getSoilModel()->getNumberOfGeologicalLayers(); $nLay++){
            $iBound[$nLay] = array();

            foreach ($activeCells as $nRow => $rowValue){
                $iBound[$nLay][$nRow] = array();
                foreach ($rowValue as $nCol => $value){
                    $iBound[$nLay][$nRow][$nCol] = (int)$value;
                }
            }
        }

        return Flopy3DArray::fromValue($iBound, $this->model->getSoilModel()->getNumberOfGeologicalLayers(), $this->model->getGridSize()->getNY(), $this->model->getGridSize()->getNY());
    }

    /**
     * @return Flopy2DArray
     */
    public function getStrt(): Flopy2DArray
    {
        return Flopy2DArray::fromValue(400.0, $this->model->getGridSize()->getNY(), $this->model->getGridSize()->getNY());
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