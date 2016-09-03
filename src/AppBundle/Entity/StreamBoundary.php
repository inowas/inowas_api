<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\Point;
use AppBundle\Model\StressPeriod;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
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

    /**
     * StreamBoundary constructor.
     * @param User|null $owner
     * @param bool $public
     */
    public function __construct(User $owner=null, $public=true)
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

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("geojson")
     * @JMS\Groups({"modelobjectdetails"})
     *
     * @return string
     */
    public function getGoeJson()
    {
        return $this->geometry->toJson();
    }

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
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
                if ($value == true){
                    $stressPeriodData[] = RivStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getCond(), $stressPeriod->getRbot());
                }
            }
        }

        return $stressPeriodData;
    }
}
