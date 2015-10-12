<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="inowas_soil_profile")
 */
class SoilProfile extends ModelObject
{
    /**
     * @var Point
     *
     * @ORM\Column(name="point", type="point", nullable=true)
     */
    private $point;

    /**
     * @ORM\ManyToOne(targetEntity="SoilProfileLayer")
     */
    private $soilProfileLayers;

    /**
     * Set point
     *
     * @param point $point
     * @return SoilProfile
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
     * Set soilProfileLayers
     *
     * @param SoilProfileLayer $soilProfileLayers
     * @return SoilProfile
     */
    public function setSoilProfileLayers(SoilProfileLayer $soilProfileLayers = null)
    {
        $this->soilProfileLayers = $soilProfileLayers;

        return $this;
    }

    /**
     * Get soilProfileLayers
     *
     * @return SoilProfileLayer
     */
    public function getSoilProfileLayers()
    {
        return $this->soilProfileLayers;
    }
}
