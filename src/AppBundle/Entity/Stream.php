<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 */
class Stream extends BoundaryModelObject
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
    private $line;

    /**
     * Set startingPoint
     *
     * @param point $startingPoint
     * @return Stream
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
     * @param LineString $line
     * @return Stream
     */
    public function setLine(LineString $line)
    {
        $this->line = $line;

        return $this;
    }

    /**
     * Get line
     *
     * @return LineString
     */
    public function getLine()
    {
        return $this->line;
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
        if (!is_null($this->line))
        {
            $line = $this->line->toArray();
            $line["type"] = $this->line->getType();
            $line["srid"] = $this->line->getSrid();
        }
        return $line;
    }
}
