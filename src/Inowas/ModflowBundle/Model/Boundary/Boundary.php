<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Model\ModelObject;
use Inowas\ModflowBundle\Model\TimeUnit;

abstract class Boundary extends ModelObject implements BoundaryInterface
{
    /** @var string */
    private $type = 'bnd';

    /** @var  ArrayCollection */
    protected $observationPoints;

    public function __construct() {
        parent::__construct();
        $this->observationPoints = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param ObservationPoint $observationPoint
     * @return Boundary
     */
    public function addObservationPoint(ObservationPoint $observationPoint): Boundary {
        $this->observationPoints->add($observationPoint);
        return $this;
    }

    /**
     * @param Point $point
     * @return ObservationPoint
     */
    public function getObservationPoint(Point $point = null): ObservationPoint {

        if (is_null($point)){
            if ($this->observationPoints->count() > 0){
                return $this->observationPoints->first();
            }

            $observationPoint = ObservationPointFactory::create();
            $this->addObservationPoint($observationPoint);
            return $observationPoint;
        }

        /** @var ObservationPoint $observationPoint */
        foreach ($this->observationPoints as $observationPoint){
            if ($observationPoint->getGeometry() == $point){
                return $observationPoint;
            }
        }

        $observationPoint = ObservationPointFactory::create()->setGeometry($point);
        $this->addObservationPoint($observationPoint);
        return $observationPoint;
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

    /**
     * @param \DateTime $globalStart
     * @param TimeUnit $timeUnit
     * @param int $totim
     * @return mixed
     */
    public function getStressPeriodData(\DateTime $globalStart, TimeUnit $timeUnit, int $totim){
        /** @var StressPeriod $stressPeriod */
        foreach ($this->getStressPeriods() as $stressPeriod){
            if ($stressPeriod->getTotalTimeStart($globalStart, $timeUnit) === $totim){
                return $this->generateStressPeriodData($stressPeriod, $this->activeCells);
            }
        }

        return null;
    }
}