<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\WellBoundary;

class WelPackageAdapter
{

    /** @var  ModFlowModel $model */
    protected $model;

    /**
     * WelPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModFlowModel $model){
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
        $wells = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof WellBoundary){
                $wells[] = $boundary;
            }
        }

        $stress_period_data = array();
        /** @var WellBoundary $well */

        foreach ($wells as $well) {
            $stress_period_data = $well->addStressPeriodData($stress_period_data, $this->model->getStressPeriods());
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