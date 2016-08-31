<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\ModFlowModel;

class GhbPackageAdapter
{

    /** @var ModFlowModel $model */
    private $model;

    /**
     * GhbPackageAdapter constructor.
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
        $boundaries = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof GeneralHeadBoundary){
                $boundaries[] = $boundary;
            }
        }

        /** @var GeneralHeadBoundary $b */
        $stress_period_data = array();
        foreach ($boundaries as $boundary) {
            $stress_period_data = $boundary->addStressPeriodData($stress_period_data, $this->model->getStressPeriods());
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