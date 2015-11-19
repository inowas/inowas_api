<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="geological_units")
 * @JMS\ExclusionPolicy("all")
 */
class GeologicalUnit
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @JMS\Expose
     */
    private $id;
    
    /**
     * @var $elevation
     *
     * @ORM\Column(name="top_elevation", type="float", nullable=true)
     */
    private $topElevation;

    /**
     * @var $elevation
     *
     * @ORM\Column(name="bottom_elevation", type="float", nullable=true)
     */
    private $bottomElevation;

    /**
     * @var GeologicalPoint
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\GeologicalPoint", inversedBy="soilProfileLayers")
     */
    private $geologicalPoint;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Layer", mappedBy="soilProfileLayer")
     */
    private $layer;

    public function __construct()
    {
        $this->layer = new ArrayCollection();
    }

    /**
     * Set topElevation
     *
     * @param float $topElevation
     * @return GeologicalPoint
     */
    public function setTopElevation($topElevation)
    {
        $this->topElevation = $topElevation;

        return $this;
    }

    /**
     * Get topElevation
     *
     * @return float 
     */
    public function getTopElevation()
    {
        return $this->topElevation;
    }

    /**
     * Set bottomElevation
     *
     * @param float $bottomElevation
     * @return GeologicalPoint
     */
    public function setBottomElevation($bottomElevation)
    {
        $this->bottomElevation = $bottomElevation;

        return $this;
    }

    /**
     * Get bottomElevation
     *
     * @return float 
     */
    public function getBottomElevation()
    {
        return $this->bottomElevation;
    }

    /**
     * Add layer
     *
     * @param \AppBundle\Entity\Layer $layer
     * @return GeologicalPoint
     */
    public function addLayer(Layer $layer)
    {
        $this->layer[] = $layer;
        return $this;
    }

    /**
     * Remove layer
     *
     * @param \AppBundle\Entity\Layer $layer
     */
    public function removeLayer(Layer $layer)
    {
        $this->layer->removeElement($layer);
    }

    /**
     * Get layer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLayer()
    {
        return $this->layer;
    }

    /**
     * Set geologicalPoint
     *
     * @param \AppBundle\Entity\GeologicalPoint $geologicalPoint
     * @return GeologicalPoint
     */
    public function setGeologicalPoint(GeologicalPoint $geologicalPoint = null)
    {
        $this->geologicalPoint = $geologicalPoint;

        return $this;
    }

    /**
     * Get geologicalPoint
     *
     * @return \AppBundle\Entity\GeologicalPoint 
     */
    public function getGeologicalPoint()
    {
        return $this->geologicalPoint;
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}
