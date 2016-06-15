<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 */
class ConstantHeadBoundary extends BoundaryModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist", "boundarylist"})
     */
    protected $type = 'CHB';

    /**
     * @var LineString
     *
     * @ORM\Column(name="geometry", type="linestring", nullable=true)
     */
    private $geometry;

    /**
     * @var ArrayCollection GeologicalLayer
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalLayer")
     * @ORM\JoinTable(name="constant_head_boundaries_geological_layers",
     *     joinColumns={@ORM\JoinColumn(name="constant_head_boundary_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="geological_layer_id", referencedColumnName="id")}
     *     )
     * @JMS\Groups({"list", "details", "modelobjectdetails"})
     * @JMS\MaxDepth(2)
     **/
    protected $geologicalLayers;

    /**
     * Boundary constructor.
     * @param User $owner
     * @param bool $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        parent::__construct($owner, $public);

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
     * @return $this
     */
    public function setGeometry(LineString $geometry)
    {
        $this->geometry = $geometry;
        return $this;
    }

    /**
     * Add geologicalLayer
     *
     * @param \AppBundle\Entity\GeologicalLayer $geologicalLayer
     * @return $this
     */
    public function addGeologicalLayer(\AppBundle\Entity\GeologicalLayer $geologicalLayer)
    {
        if (!$this->geologicalLayers->contains($geologicalLayer)){
            $this->geologicalLayers[] = $geologicalLayer;
        }
        return $this;
    }

    /**
     * @param GeologicalLayer $geologicalLayer
     * @return $this
     */
    public function removeGeologicalLayer(\AppBundle\Entity\GeologicalLayer $geologicalLayer)
    {
        if ($this->geologicalLayers->contains($geologicalLayer)){
            $this->geologicalLayers->removeElement($geologicalLayer);
        }
        return $this;
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
    
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("geometry")
     * @JMS\Groups({"modelobjectdetails", "boundarylist"})
     *
     * @return string
     */
    public function serializeDeserializeGeometry()
    {
        $geometry = null;

        if (!is_null($this->geometry))
        {
            $geometry = $this->geometry->toArray();
            $geometry["type"] = $this->geometry->getType();
            $geometry["srid"] = $this->geometry->getSrid();
        }
        return $geometry;
    }
}
