<?php

namespace Inowas\FlopyBundle\Model\Adapter;


use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;

class GhbPackageAdapter
{

    /** @var ModflowModel $model */
    private $model;

    /**
     * GhbPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModflowModel $model)
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
        $boundaries = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof GeneralHeadBoundary){
                $boundaries[] = $boundary;
            }
        }

        /** @var GeneralHeadBoundary $boundary */
        $stress_period_data = array();
        foreach ($boundaries as $boundary) {
            $stress_period_data = $boundary->aggregateStressPeriodData($stress_period_data, $this->model->getGlobalStressPeriods());
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
     * @return string
     */
    public function getExtension(): string
    {
        return 'ghb';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 23;
    }
}
