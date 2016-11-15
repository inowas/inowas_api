<?php

namespace Inowas\ModflowBundle\Model;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Inowas\ModflowBundle\Model\ValueObject\RchStressPeriod;

class RechargeBoundary extends Boundary
{
    /** @var Polygon */
    private $geometry;

    /** @var ArrayCollection */
    private $stressPeriods;

    public function __construct()
    {
        parent::__construct();
        $this->stressPeriods = new ArrayCollection();
    }

    /** @return Polygon */
    public function getGeometry(){
        return $this->geometry;
    }

    /**
     * @param Polygon $geometry
     * @return $this
     */
    public function setGeometry(Polygon $geometry)
    {
        $this->geometry = $geometry;

        return $this;
    }

    /** @return ArrayCollection */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
    }

    /**
     * @param StressPeriodInterface $stressPeriod
     * @return $this
     */
    public function addStressPeriod(StressPeriodInterface $stressPeriod)
    {
        if ($stressPeriod instanceof RchStressPeriod){
            $this->stressPeriods->add($stressPeriod);
        }
        return $this;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof RchStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type RchStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = $stressPeriod->getRech()->toReducedArray();

        return $stressPeriodData;
    }
}
