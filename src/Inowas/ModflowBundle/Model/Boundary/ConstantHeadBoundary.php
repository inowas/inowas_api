<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\StressPeriod;
use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Inowas\ModflowBundle\Model\ValueObject\ChdStressPeriod;
use Inowas\ModflowBundle\Model\ValueObject\ChdStressPeriodData;

class ConstantHeadBoundary extends Boundary
{
    /** @var LineString */
    protected $geometry;

    /** @var array */
    protected $layerNumbers;

    /**
     * @return LineString
     */
    public function getGeometry(): LineString
    {
        return $this->geometry;
    }

    /**
     * @param LineString $geometry
     * @return ConstantHeadBoundary
     */
    public function setGeometry(LineString $geometry): ConstantHeadBoundary
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * @return array
     */
    public function getLayerNumbers(): array
    {
        return $this->layerNumbers;
    }

    /**
     * @param array $layerNumbers
     * @return ConstantHeadBoundary
     */
    public function setLayerNumbers(array $layerNumbers): ConstantHeadBoundary
    {
        $this->layerNumbers = $layerNumbers;
        return $this;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof ChdStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type ChdStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    $stressPeriodData[] = ChdStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getShead(), $stressPeriod->getEhead());
                }
            }
        }

        return $stressPeriodData;
    }
}
