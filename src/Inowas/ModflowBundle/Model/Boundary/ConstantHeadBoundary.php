<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\ValueObject\ChdStressPeriodData;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ActiveCells;
use JMS\Serializer\Annotation as JMS;

class ConstantHeadBoundary extends Boundary
{
    /**
     * @var string
     * @JMS\Groups("details")
     */
    private $type = 'CHD';

    /** @var LineString */
    protected $geometry;

    /** @var array */
    protected $layerNumbers = array(0);

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

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
     * @param ChdStressPeriod $chdStressPeriod
     * @param Point $point
     * @return ConstantHeadBoundary
     */
    public function addStressPeriod(ChdStressPeriod $chdStressPeriod, Point $point): ConstantHeadBoundary {
        $observationPoint = $this->getObservationPoint($point);
        $observationPoint->addStressPeriod($chdStressPeriod);
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

        foreach ($this->getLayerNumbers() as $layerNumber){
            foreach ($activeCells->toArray() as $nRow => $row){
                foreach ($row as $nCol => $value){
                    if ($value === true){
                        if (is_int($nRow) && is_int($nCol)) {
                            $stressPeriodData[] = ChdStressPeriodData::create($layerNumber, $nRow, $nCol, $stressPeriod->getShead(), $stressPeriod->getEhead());
                        }
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
