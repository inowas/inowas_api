<?php

namespace Inowas\Flopy\Model\Adapter;

use Inowas\ModflowBundle\Model\Boundary\ConstantHeadBoundary;
use Inowas\ModflowBundle\Model\Boundary\GeneralHeadBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;

class ChdPackageAdapter
{

    /** @var  ModflowModel $model */
    protected $model;

    /**
     * ChdPackageAdapter constructor.
     * @param ModflowModel $model
     */
    public function __construct(ModflowModel $model){
        $this->model = $model;
    }

    /**
     * @return array
     */
    public function getStressPeriodData()
    {
        $boundaries = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof ConstantHeadBoundary){
                $boundaries[] = $boundary;
            }
        }

        $globalStressPeriods = $this->model->getGlobalStressPeriods();
        $stress_period_data = array();

        foreach ($globalStressPeriods->getTotalTimesStart() as $key => $startTime){
            /** @var ConstantHeadBoundary $boundary */
            foreach ($boundaries as $boundary) {
                $data = $boundary->getStressPeriodData($this->model->getStart(), $this->model->getTimeUnit(), $startTime);
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
     * @return string
     */
    public function getExtension(): string
    {
        return 'chd';
    }

    /**
     * @return null
     */
    public function getOptions()
    {
        return null;
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 24;
    }
}
