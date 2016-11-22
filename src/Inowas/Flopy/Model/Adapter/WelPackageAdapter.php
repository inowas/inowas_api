<?php

namespace Inowas\Flopy\Model\Adapter;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;

class WelPackageAdapter
{

    /** @var  ModFlowModel $model */
    protected $model;

    /**
     * WelPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModflowModel $model){
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
    public function getStressPeriodData(): array
    {
        $wells = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof WellBoundary){
                $wells[] = $boundary;
            }
        }

        $globalStressPeriods = $this->model->getGlobalStressPeriods();
        $stress_period_data = array();
        foreach ($globalStressPeriods->getTotalTimesStart() as $key => $startTime){
            /** @var WellBoundary $well */
            foreach ($wells as $well) {
                $data =  $well->getStressPeriodData($this->model->getStart(), $this->model->getTimeUnit(), $startTime);

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
        return 'wel';
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
        return 20;
    }
}
