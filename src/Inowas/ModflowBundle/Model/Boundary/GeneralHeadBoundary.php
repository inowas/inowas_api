<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Inowas\ModflowBundle\Model\ValueObject\GhbStressPeriodData;

class GeneralHeadBoundary extends Boundary
{
    /** @var string */
    private $type = 'GHB';

    /** @var LineString */
    protected $geometry;

    /** @var array */
    protected $layerNumbers;

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

    // Todo: Adapt this
    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof GhbStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type GhbStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    $stressPeriodData[] = GhbStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getCond());
                }
            }
        }

        return $stressPeriodData;
    }
}
