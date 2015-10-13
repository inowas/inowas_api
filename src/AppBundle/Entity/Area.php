<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="inowas_area")
 */
class Area extends ModelObject
{
    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="polygon", nullable=true)
     */
    private $geometry;

    /**
     * @var AreaType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\AreaType")
     */
    private $areaType;

    /**
     * @return Polygon
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param Polygon $geometry
     */
    public function setGeometry(Polygon $geometry)
    {
        $this->geometry = $geometry;
    }

    /**
     * Set areaType
     *
     * @param \AppBundle\Entity\AreaType $areaType
     * @return Area
     */
    public function setAreaType(AreaType $areaType = null)
    {
        $this->areaType = $areaType;

        return $this;
    }

    /**
     * Get areaType
     *
     * @return \AppBundle\Entity\AreaType 
     */
    public function getAreaType()
    {
        return $this->areaType;
    }
}
