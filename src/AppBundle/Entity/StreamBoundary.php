<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\Point;
use AppBundle\Model\StressPeriod;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriodData;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 */
class StreamBoundary extends BoundaryModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'RIV';

    /**
     * @var Point
     *
     * @ORM\Column(name="starting_point", type="point", nullable=true)
     */
    private $startingPoint;

    /**
     * @var LineString
     *
     * @ORM\Column(name="line", type="linestring", nullable=true)
     */
    private $geometry;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="stress_periods", type="riv_stress_periods", nullable=true)
     */
    private $stressPeriods;


    public function __construct(User $owner=null, $public=null)
    {
        parent::__construct($owner, $public);
        $this->stressPeriods = new ArrayCollection();
    }

    /**
     * Set startingPoint
     *
     * @param point $startingPoint
     * @return $this
     */
    public function setStartingPoint($startingPoint)
    {
        $this->startingPoint = $startingPoint;

        return $this;
    }

    /**
     * Get startingPoint
     *
     * @return point 
     */
    public function getStartingPoint()
    {
        return $this->startingPoint;
    }

    /**
     * Set line
     *
     * @param LineString $geometry
     * @return StreamBoundary
     */
    public function setGeometry(LineString $geometry)
    {
        $this->geometry = $geometry;

        return $this;
    }

    /**
     * Get line
     *
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
     * @param $spd
     * @param ArrayCollection $globalStressPeriods
     * @return mixed
     */
    public function getStressPeriodData($spd, ArrayCollection $globalStressPeriods){

        $rivStressPeriods = $this->stressPeriods;

        /** @var RivStressPeriod $rivStressPeriod */
        foreach ($rivStressPeriods as $rivStressPeriod){
            /** @var StressPeriod $globalStressPeriod */
            foreach ($globalStressPeriods as $key => $globalStressPeriod){
                if ($rivStressPeriod->getDateTimeBegin() == $globalStressPeriod->getDateTimeBegin()){

                    if (! isset($spd[$key])){
                        $spd[$key] = array();
                    }

                    $spd[$key] = array_merge($spd[$key], $this->generateStressPeriodData($rivStressPeriod, $this->activeCells));

                    break;
                }
            }
        }

        return $spd;
    }

    /**
     * @param RivStressPeriod $rivStressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(RivStressPeriod $rivStressPeriod, ActiveCells $activeCells){

        $spd = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value == true){
                    $spd[] = RivStressPeriodData::create(0, $nRow, $nCol, $rivStressPeriod->getStage(), $rivStressPeriod->getCond(), $rivStressPeriod->getRbot());
                }
            }
        }

        return $spd;
    }

    /**
     * @param RivStressPeriod $sp
     * @return $this
     */
    public function addStressPeriod(RivStressPeriod $sp)
    {
        $this->stressPeriods->add($sp);
        return $this;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("starting_point")
     * @JMS\Groups({"modelobjectdetails"})
     *
     * @return string
     */
    public function serializeDeserializeStartingPoint()
    {
        $sp = null;
        if (!is_null($this->startingPoint))
        {
            $sp = $this->startingPoint->toArray();
            $sp["type"] = $this->startingPoint->getType();
            $sp["srid"] = $this->startingPoint->getSrid();
        }
        return $sp;
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("line")
     * @JMS\Groups({"modelobjectdetails"})
     *
     * @return string
     */
    public function serializeDeserializeLine()
    {
        $line = null;
        if (!is_null($this->geometry))
        {
            $line = $this->geometry->toArray();
            $line["type"] = $this->geometry->getType();
            $line["srid"] = $this->geometry->getSrid();
        }
        return $line;
    }
}
