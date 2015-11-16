<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="inowas_soil_profile_layer")
 */
class SoilProfileLayer extends ModelObject
{
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
     * @var SoilProfile
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\SoilProfile", inversedBy="soilProfileLayers")
     */
    private $soilProfile;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Layer", mappedBy="soilProfileLayer")
     */
    private $layer;

    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);
        $this->layer = new ArrayCollection();
    }

    /**
     * Set topElevation
     *
     * @param float $topElevation
     * @return SoilProfileLayer
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
     * @return SoilProfileLayer
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
     * @return SoilProfileLayer
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
     * Set soilProfile
     *
     * @param \AppBundle\Entity\SoilProfile $soilProfile
     * @return SoilProfileLayer
     */
    public function setSoilProfile(SoilProfile $soilProfile = null)
    {
        $this->soilProfile = $soilProfile;

        return $this;
    }

    /**
     * Get soilProfile
     *
     * @return \AppBundle\Entity\SoilProfile 
     */
    public function getSoilProfile()
    {
        return $this->soilProfile;
    }
}
