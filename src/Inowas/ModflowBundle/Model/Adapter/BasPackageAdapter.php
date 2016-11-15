<?php

namespace Inowas\ModflowBundle\Model\Adapter;

use Inowas\ModflowBundle\Model\Area;
use Inowas\ModflowBundle\Model\ModFlowModel;
use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Inowas\ModflowBundle\Model\ValueObject\Flopy3DArray;
use Inowas\ModflowBundle\Model\ValueObject\IBound;

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
     * @return IBound
     */
    public function getIbound(): IBound
    {
        if (! $this->model->getArea() instanceof Area || ! $this->model->getArea()->getActiveCells() instanceof ActiveCells){
            return IBound::fromValue(
                1,
                $this->model->getSoilModel()->getLayers()->count(),
                $this->model->getGridSize()->getNY(),
                $this->model->getGridSize()->getNX());
        }

        return IBound::fromActiveCells(
            $this->model->getArea()->getActiveCells(),
            $this->model->getSoilModel()->getLayers()->count(),
            $this->model->getGridSize()->getNY(),
            $this->model->getGridSize()->getNX()
        );
    }

    /**
     * @return Flopy3DArray
     */
    public function getStrt(): Flopy3DArray
    {
        return Flopy3DArray::fromValue(
            400.0,
            $this->model->getSoilModel()->getLayers()->count(),
            $this->model->getGridSize()->getNY(),
            $this->model->getGridSize()->getNX()
        );
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
