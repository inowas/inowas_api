<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Polygon;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="inowas_lake")
 */
class Lake extends ModelObject
{
    /**
     * @var Polygon
     *
     * @ORM\Column(name="geometry", type="polygon", nullable=true)
     */
    private $geometry;

    /**
     * @var ObservationPoint
     *
     * @ORM\ManyToOne(targetEntity="ObservationPoint")
     */
    private $observationPoints;

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
     * Set observationPoints
     *
     * @param \AppBundle\Entity\ObservationPoint $observationPoints
     * @return Lake
     */
    public function setObservationPoints(ObservationPoint $observationPoints = null)
    {
        $this->observationPoints = $observationPoints;

        return $this;
    }

    /**
     * Get observationPoints
     *
     * @return \AppBundle\Entity\ObservationPoint 
     */
    public function getObservationPoints()
    {
        return $this->observationPoints;
    }
}
