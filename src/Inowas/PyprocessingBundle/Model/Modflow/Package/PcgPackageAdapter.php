<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;

class PcgPackageAdapter
{
    /** @var  ModFlowModel */
    protected $model;

    /**
     * PcgPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return int
     */
    public function getMxiter(): int
    {
        return 50;
    }

    /**
     * @return int
     */
    public function getIter1(): int
    {
        return 30;
    }

    /**
     * @return int
     */
    public function getNpcond(): int
    {
        return 1;
    }

    /**
     * @return float
     */
    public function getHclose(): float
    {
        return 1e-5;
    }

    /**
     * @return float
     */
    public function getRclose(): float
    {
        return 1e-5;
    }

    /**
     * @return float
     */
    public function getRelax(): float
    {
        return 1.0;
    }

    /**
     * @return int
     */
    public function getNbpol(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getIprpcg(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getMutpcg(): int
    {
        return 3;
    }

    /**
     * @return float
     */
    public function getDamp(): float
    {
        return 1.0;
    }

    /**
     * @return float
     */
    public function getDampt(): float
    {
        return 1.0;
    }

    /**
     * @return int
     */
    public function getIhcofadd(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'pcg';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 27;
    }
}
