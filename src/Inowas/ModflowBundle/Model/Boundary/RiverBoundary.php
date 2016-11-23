<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\ValueObject\RivStressPeriodData;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ActiveCells;

class RiverBoundary extends Boundary
{
    /** @var string */
    private $type = 'RIV';

    /** @var LineString */
    private $geometry;

    /**
     * @return string
     */
    public function getType(): string {
        return $this->type;
    }

    /**
     * @param LineString $geometry
     * @return RiverBoundary
     */
    public function setGeometry(LineString $geometry): RiverBoundary {
        $this->geometry = $geometry;
        return $this;
    }

    /** @return LineString */
    public function getGeometry(): LineString{
        return $this->geometry;
    }

    /**
     * @param RivStressPeriod $rivStressPeriod
     * @param Point $point
     * @return RiverBoundary
     */
    public function addStressPeriod(RivStressPeriod $rivStressPeriod, Point $point): RiverBoundary {
        $observationPoint = $this->getObservationPoint($point);
        $observationPoint->addStressPeriod($rivStressPeriod);
        return $this;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells): array {

        if (! $stressPeriod instanceof RivStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type RivStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    if (is_int($nRow) && is_int($nCol)) {
                        $stressPeriodData[] = RivStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getConductivity(), $stressPeriod->getBottomElevation());
                    }
                }
            }
        }

        return $stressPeriodData;
    }

    /**
     * @return null|string
     */
    public function getJsonGeometry(){
        if (! $this->geometry instanceof LineString){
            return null;
        }

        return $this->geometry->toJson();
    }
}
