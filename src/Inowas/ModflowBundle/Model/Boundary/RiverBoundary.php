<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Inowas\ModflowBundle\Model\ValueObject\RivStressPeriodData;

class RiverBoundary extends Boundary
{
    /** @var string */
    private $type = 'RIV';

    /** @var LineString */
    private $geometry;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param LineString $geometry
     * @return RiverBoundary
     */
    public function setGeometry(LineString $geometry)
    {
        $this->geometry = $geometry;
        return $this;
    }

    /** @return LineString */
    public function getGeometry()
    {
        return $this->geometry;
    }

    // Todo: Adapt this
    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof RivStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type RivStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    $stressPeriodData[] = RivStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getConductivity(), $stressPeriod->getBottomElevation());
                }
            }
        }

        return $stressPeriodData;
    }
}
