<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="streams")
 * @JMS\ExclusionPolicy("all")
 */
class Stream extends ModelObject
{
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
}
