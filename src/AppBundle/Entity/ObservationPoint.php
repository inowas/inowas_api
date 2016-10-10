<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="observation_points")
 */
class ObservationPoint extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'observationPoint';

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $geometry;

    /**
     * @var $elevation
     *
     * @ORM\Column(name="elevation", type="float", nullable=true)
     * @JMS\Groups({"modelobjectdetails"})
     */
    private $elevation;

    /**
     * ObservationPoint constructor.
     * @param User|null $owner
     * @param bool|false $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        parent::__construct($owner, $public);

        $this->modelObjects = new ArrayCollection();
    }

    /**
     * Set point
     *
     * @param point $geometry
     * @return ObservationPoint
     */
    public function setGeometry($geometry)
    {
        $this->geometry = $geometry;

        return $this;
    }

    /**
     * Get point
     *
     * @return point 
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * Set elevation
     *
     * @param float $elevation
     * @return ObservationPoint
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * Get elevation
     *
     * @return float 
     */
    public function getElevation()
    {
        return $this->elevation;
    }
    
    /**
     * @return string
     */
    public function getType()
    {
        return 'ObservationPoint';
    }

    /**
     * @JMS\VirtualProperty()
     * @JMS\SerializedName("point")
     * @JMS\Groups({"modelobjectdetails"})
     */
    public function convertPointToPoint()
    {
        if (!is_null($this->geometry))
        {
            $point = new Point($this->geometry->getX(),$this->geometry->getY());
            $point->setSrid($this->geometry->getSrid());
            return $point;
        }

        return null;
    }
}
