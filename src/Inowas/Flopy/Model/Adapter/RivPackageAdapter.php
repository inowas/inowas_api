<?php

namespace Inowas\Flopy\Model\Adapter;

use Inowas\ModflowBundle\Model\Boundary\RiverBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;

class RivPackageAdapter
{

    /** @var ModflowModel $model */
    private $model;

    /**
     * RivPackageAdapter constructor.
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
        $rivers = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof RiverBoundary){
                $rivers[] = $boundary;
            }
        }

        $globalStressPeriods = $this->model->getGlobalStressPeriods();
        $stress_period_data = array();

        foreach ($globalStressPeriods->getTotalTimesStart() as $key => $startTime){
            /** @var RiverBoundary $river */
            foreach ($rivers as $river) {
                $data = $river->getStressPeriodData($this->model->getStart(), $this->model->getTimeUnit(), $startTime);
                if (! is_null($data)){
                    if (! array_key_exists($key, $stress_period_data)){
                        $stress_period_data[$key] = array();
                    }

                    $stress_period_data[$key] = array_merge($stress_period_data[$key], $data);
                }
            }
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
