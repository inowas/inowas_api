<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Boundary;
use Inowas\ModflowBundle\Model\StressPeriod;
use Inowas\ModflowBundle\Model\StressPeriodInterface;
use Inowas\ModflowBundle\Model\ValueObject\ActiveCells;
use Inowas\ModflowBundle\Model\ValueObject\RivStressPeriod;
use Inowas\ModflowBundle\Model\ValueObject\RivStressPeriodData;

class RiverBoundary extends Boundary
{
    /** @var LineString */
    private $geometry;

    /** @var ArrayCollection */
    private $stressPeriods;

    public function __construct()
    {
        parent::__construct();
        $this->stressPeriods = new ArrayCollection();
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

    /**
     * @return LineString
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
    }

    /**
     * @param StressPeriodInterface $stressPeriod
     * @return $this
     */
    public function addStressPeriod(StressPeriodInterface $stressPeriod)
    {
        if ($stressPeriod instanceof RivStressPeriod){
            $this->stressPeriods->add($stressPeriod);
        }
        return $this;
    }

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
                    $stressPeriodData[] = RivStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getCond(), $stressPeriod->getRbot());
                }
            }
        }

        return $stressPeriodData;
    }
}
