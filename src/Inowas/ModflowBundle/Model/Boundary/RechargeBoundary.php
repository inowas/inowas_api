<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ActiveCells;

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
     * @param RchStressPeriod $welStressPeriod
     * @return RechargeBoundary
     */
    public function addStressPeriod(RchStressPeriod $welStressPeriod): RechargeBoundary {
        $observationPoint = $this->getObservationPoint();
        $observationPoint->addStressPeriod($welStressPeriod);
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

        $data = array();
        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    if (is_int($nRow) && is_int($nCol)){
                        $data[$nRow][$nCol] = $stressPeriod->getRecharge();
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @return null|string
     */
    public function getJsonGeometry(){
        if (! $this->geometry instanceof Polygon){
            return null;
        }

        return $this->geometry->toJson();
    }
}
