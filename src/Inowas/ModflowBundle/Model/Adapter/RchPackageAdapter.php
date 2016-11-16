<?php

namespace Inowas\ModflowBundle\Model\Adapter;

use Inowas\ModflowBundle\Model\Boundary\RechargeBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;

class RchPackageAdapter
{
    /**
     * @var ModFlowModel
     */
    private $model;

    /**
     * RchPackageAdapter constructor.
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
     * @return int
     */
    public function getNrchop(): int
    {
        return 3;
    }

    /**
     * @return array
     */
    public function getRech(): array
    {
        $boundaries = array();
        foreach ($this->model->getBoundaries() as $boundary){
            if ($boundary instanceof RechargeBoundary){
                $boundaries[] = $boundary;
            }
        }

        $rech = array();

        /** @var RechargeBoundary $boundary */
        foreach ($boundaries as $boundary) {
            $rech = $boundary->aggregateStressPeriodData($rech, $this->model->getGlobalStressPeriods());
        }

        return $rech;
    }

    /**
     * @return int
     */
    public function getIrch(): int
    {
        return 0;
    }

    /**
     * @return string
     */
    public function getExtension(): string
    {
        return 'rch';
    }

    /**
     * @return int
     */
    public function getUnitnumber(): int
    {
        return 19;
    }
}
