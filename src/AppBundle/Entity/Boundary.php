<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="inowas_boundary")
 */
class Boundary extends ModelObject
{
    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="linestring", nullable=true)
     */
    private $geometry;

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
}
