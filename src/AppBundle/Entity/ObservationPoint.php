<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="inowas_observation_point")
 */
class ObservationPoint extends ModelObject
{

    /**
     * @var Point
     *
     * @ORM\Column(name="point", type="point", nullable=true)
     */
    private $point;

    /**
     * @var $elevation
     *
     * @ORM\Column(name="elevation", type="float", nullable=true)
     */
    private $elevation;


    /**
     * Set point
     *
     * @param point $point
     * @return ObservationPoint
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return point 
     */
    public function getPoint()
    {
        return $this->point;
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
}
