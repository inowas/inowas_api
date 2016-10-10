<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\StreamBoundary;

class RivPackageAdapter
{

    /** @var ModFlowModel $model */
    private $model;

    /**
     * RivPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model)
    {
        $this->model = $model;
    }

    /**
     * @return int
     */
    public function getIpakcb(): int
    {
        return 0;
    }

    /**
     * @return array
     */
    public function getStressPeriodData()
    {
        $rivers = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof StreamBoundary){
                $rivers[] = $boundary;
            }
        }

        /** @var StreamBoundary $river */
        $stress_period_data = array();
        foreach ($rivers as $river) {
            $stress_period_data = $river->aggregateStressPeriodData($stress_period_data, $this->model->getStressPeriods());
        }

        return $stress_period_data;
    }

    /**
     * @return null
     */
    public function getDtype()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getOptions()
    {
        return null;
    }

    /**
     * @return null
     */
    public function getNaux()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'riv';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 18;
    }
}
