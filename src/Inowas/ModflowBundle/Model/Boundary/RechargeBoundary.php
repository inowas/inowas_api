<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;

class RechargeBoundary extends Boundary
{
    /** @var string */
    private $type = 'RCH';

    /** @var Polygon */
    private $geometry;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /** @return Polygon */
    public function getGeometry(): Polygon
    {
        return $this->geometry;
    }

    /**
     * @param Polygon $geometry
     * @return RechargeBoundary
     */
    public function setGeometry(Polygon $geometry): RechargeBoundary
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

        $stressPeriodData = $stressPeriod->getRecharge()->toReducedArray();

        return $stressPeriodData;
    }
}
