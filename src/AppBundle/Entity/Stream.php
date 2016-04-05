<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="streams")
 */
class Stream extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'stream';

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    protected $name;

    /**
     * @var ArrayCollection Property
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="modelObject", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Property>")
     */
    protected $properties;

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
