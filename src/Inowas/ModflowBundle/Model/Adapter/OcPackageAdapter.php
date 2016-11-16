<?php

namespace Inowas\ModflowBundle\Model\Adapter;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\StressPeriodOutputControl;

class OcPackageAdapter
{

    /**
     * @var ModFlowModel $model
     */
    protected $model;

    /**
     * OcPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModflowModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return int
     */
    public function getIhedfm(): int
    {
        return 0;
    }

    /**
     * @return int
     */
    public function getIddnfm(): int
    {
        return 0;
    }

    /**
     * @return string|null
     */
    public function getChedfm()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getCddnfm()
    {
        return null;
    }

    /**
     * @return string|null
     */
    public function getCboufm()
    {
        return null;
    }

    /**
     * @return boolean
     */
    public function isCompact(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function getStressPeriodData(): array
    {
        return array(
            StressPeriodOutputControl::create(0, 0, StressPeriodOutputControl::SAVE_HEAD)
        );
    }

    /**
     * @return array
     */
    public function getExtension(): array
    {
        return ['oc', 'hds', 'ddn', 'cbc'];
    }

    /**
     * @return array
     */
    public function getUnitnumber(): array
    {
        return [14, 51, 52, 53];
    }
}
