<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Model\ModelObject;

abstract class Boundary extends ModelObject  implements BoundaryInterface
{
    /** @var  ArrayCollection */
    protected $observationPoints;

    public function __construct() {
        parent::__construct();
        $this->observationPoints = new ArrayCollection();
    }

    /**
     * @param ObservationPoint $observationPoint
     * @return ConstantHeadBoundary
     */
    public function addObservationPoint(ObservationPoint $observationPoint): Boundary {
        $this->observationPoints->add($observationPoint);
        return $this;
    }

    /** @return ArrayCollection */
    public function getObservationPoints(): ArrayCollection {
        return $this->observationPoints;
    }

    /** @return ArrayCollection */
    public function getStressPeriods(): ArrayCollection
    {
        $stressPeriods = new ArrayCollection();
        /** @var ObservationPoint $observationPoint */
        foreach ($this->observationPoints as $observationPoint){
            $stressPeriods = new ArrayCollection(array_merge(
                    $stressPeriods->toArray(),
                    $observationPoint->getStressPeriods()->toArray()
                )
            );
        }

        return $stressPeriods;
    }
}