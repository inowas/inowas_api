<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\StressPeriod;
use Inowas\ModflowBundle\Model\ValueObject\RchStressPeriod;

class RechargeBoundary extends Boundary
{
    /** @var Polygon */
    private $geometry;

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

    /**
     * @param StressPeriod $stressPeriod
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod){

        if (! $stressPeriod instanceof RchStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type RchStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = $stressPeriod->getRech()->toReducedArray();

        return $stressPeriodData;
    }
}
