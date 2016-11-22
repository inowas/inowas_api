<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\Flopy\Model\ValueObject\GhbStressPeriodData;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ActiveCells;

class GeneralHeadBoundary extends Boundary
{
    /** @var string */
    private $type = 'GHB';

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
     * @return GeneralHeadBoundary
     */
    public function setGeometry(LineString $geometry): GeneralHeadBoundary
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
     * @return GeneralHeadBoundary
     */
    public function setLayerNumbers(array $layerNumbers): GeneralHeadBoundary
    {
        $this->layerNumbers = $layerNumbers;
        return $this;
    }

    /**
     * @param GhbStressPeriod $ghbStressPeriod
     * @param Point $point
     * @return GeneralHeadBoundary
     */
    public function addStressPeriod(GhbStressPeriod $ghbStressPeriod, Point $point): GeneralHeadBoundary {
        $observationPoint = $this->getObservationPoint($point);
        $observationPoint->addStressPeriod($ghbStressPeriod);
        return $this;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells): array {

        if (! $stressPeriod instanceof GhbStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type GhbStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($this->getLayerNumbers() as $layerNumber){
            foreach ($activeCells->toArray() as $nRow => $row){
                foreach ($row as $nCol => $value){
                    if ($value === true){
                        if (is_int($nRow) && is_int($nCol)) {
                            $stressPeriodData[] = GhbStressPeriodData::create($layerNumber, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getConductivity());
                        }
                    }
                }
            }
        }

        return $stressPeriodData;
    }
}
