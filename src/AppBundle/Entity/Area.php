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
     * @var Boolean
     *
     * @ORM\Column(name="is_lake", type="boolean")
     */
    private $lake = false;

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
     * Set lake
     *
     * @param boolean $lake
     * @return Area
     */
    public function setLake($lake)
    {
        $this->lake = $lake;

        return $this;
    }

    /**
     * Get lake
     *
     * @return boolean 
     */
    public function getLake()
    {
        return $this->lake;
    }
}
