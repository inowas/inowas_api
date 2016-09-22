<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as JMS;

abstract class HeadBoundary extends BoundaryModelObject
{
    protected $type;
    protected $geometry;
    protected $stressPeriods;
    protected $geologicalLayers;

    /**
     * HeadBoundary constructor.
     * @param User|null $owner
     * @param bool $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        parent::__construct($owner, $public);
        $this->geologicalLayers = new ArrayCollection();
        $this->stressPeriods = new ArrayCollection();
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
     * @param GeologicalLayer $geologicalLayer
     * @return $this
     */
    public function addGeologicalLayer(GeologicalLayer $geologicalLayer)
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
    public function removeGeologicalLayer(GeologicalLayer $geologicalLayer)
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

        if ($this->geometry instanceof LineString) {
            $geometry = $this->geometry->toArray();
            $geometry["type"] = $this->geometry->getType();
            $geometry["srid"] = $this->geometry->getSrid();
        }
        return $geometry;
    }

    /**
     * @return ArrayCollection
     */
    public function getStressPeriods()
    {
        return $this->stressPeriods;
    }
}
