<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\Flopy\Model\ValueObject\WelStressPeriodData;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\ActiveCells;

class WellBoundary extends Boundary
{
    const TYPE_PRIVATE_WELL = "prw";
    const TYPE_PUBLIC_WELL = "puw";
    const TYPE_OBSERVATION_WELL = "ow";
    const TYPE_INDUSTRIAL_WELL = "iw";
    const TYPE_IRRIGATION_WELL = "irw";
    const TYPE_SCENARIO_MOVED_WELL = "smw";
    const TYPE_SCENARIO_NEW_INDUSTRIAL_WELL = "sniw";
    const TYPE_SCENARIO_NEW_INFILTRATION_WELL = "snifw";
    const TYPE_SCENARIO_NEW_PUBLIC_WELL = "snpw";
    const TYPE_SCENARIO_NEW_WELL = "snw";
    const TYPE_SCENARIO_REMOVED_WELL = "srw";

    /** @var string */
    private $type = 'WEL';

    /** @var string */
    protected $wellType = self::TYPE_PUBLIC_WELL;

    /** @var  Point */
    protected $geometry;

    /** @var int */
    private $layerNumber = 0;

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getWellType()
    {
        return $this->wellType;
    }

    /**
     * @param string $wellType
     * @return $this
     */
    public function setWellType($wellType)
    {
        $this->wellType = $wellType;
        return $this;
    }

    /**
     * Set point
     *
     * @param point $geometry
     * @return $this
     */
    public function setGeometry(Point $geometry){
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * @return point
     */
    public function getGeometry(){
        return $this->geometry;
    }

    /**
     * @return int
     */
    public function getLayerNumber(): int
    {
        return $this->layerNumber;
    }

    /**
     * @param int $layerNumber
     * @return WellBoundary
     */
    public function setLayerNumber(int $layerNumber): WellBoundary
    {
        $this->layerNumber = $layerNumber;
        return $this;
    }

    /**
     * @param StressPeriod $welStressPeriod
     * @return WellBoundary
     */
    public function addStressPeriod(StressPeriod $welStressPeriod): WellBoundary {
        $observationPoint = $this->getObservationPoint();
        $observationPoint->addStressPeriod($welStressPeriod);
        return $this;
    }

    /**
     * @param array $stressPeriods
     */
    public function setStressPeriods(array $stressPeriods){
        $observationPoint = $this->getObservationPoint();
        $observationPoint->setStressPeriods(new ArrayCollection());
        foreach ($stressPeriods as $stressPeriod) {
            $this->addStressPeriod($stressPeriod);
        }
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof WelStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type WelStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    if (is_int($nRow) && is_int($nCol)){
                        $stressPeriodData[] = WelStressPeriodData::create($this->layerNumber, $nRow, $nCol, $stressPeriod->getFlux());
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
        if (! $this->geometry instanceof Point){
            return null;
        }

        return $this->geometry->toJson();
    }
}
