<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="boundaries")
 */
class Boundary extends ModelObject
{
    /**
     * @var string
     * @JMS\Groups({"list", "details"})
     */
    protected $type = 'boundary';

    /**
     * @var ArrayCollection Property
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Property", mappedBy="modelObject", cascade={"persist", "remove"})
     * @JMS\Type("ArrayCollection<AppBundle\Entity\Property>")
     */
    protected $properties;

    /**
     * @var LineString
     *
     * @ORM\Column(name="geometry", type="linestring", nullable=true)
     */
    private $geometry;

    /**
     * @var ArrayCollection GeologicalLayer
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalLayer", mappedBy="boundaries")
     **/
    protected $geologicalLayers;

    /**
     * Boundary constructor.
     * @param User $owner
     * @param Project $project
     * @param bool $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);

        $this->geologicalLayers = new ArrayCollection();
    }

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

    /**
     * Add geologicalLayer
     *
     * @param \AppBundle\Entity\GeologicalLayer $geologicalLayer
     *
     * @return Boundary
     */
    public function addGeologicalLayer(\AppBundle\Entity\GeologicalLayer $geologicalLayer)
    {
        $this->geologicalLayers[] = $geologicalLayer;

        return $this;
    }

    /**
     * Remove geologicalLayer
     *
     * @param \AppBundle\Entity\GeologicalLayer $geologicalLayer
     */
    public function removeGeologicalLayer(\AppBundle\Entity\GeologicalLayer $geologicalLayer)
    {
        $this->geologicalLayers->removeElement($geologicalLayer);
    }

    /**
     * Get geologicalLayers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGeologicalLayers()
    {
        return $this->geologicalLayers;
    }
}
