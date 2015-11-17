<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="inowas_boundary")
 * @JMS\ExclusionPolicy("all")
 */
class Boundary extends ModelObject
{
    /**
     * @var LineString
     *
     * @ORM\Column(name="geometry", type="linestring", nullable=true)
     */
    private $geometry;

    /**
     * @return LineString
     */
    public function getGeometry()
    {
        return $this->geometry;
    }

    /**
     * @param LineString $geometry
     */
    public function setGeometry(LineString $geometry)
    {
        $this->geometry = $geometry;
    }
}
