<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="inowas_layer")
 */
class Layer extends ModelObject
{
    /**
     * @var ArrayCollection SoilProfile
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\SoilProfileLayer", inversedBy="layer")
     * @ORM\JoinTable(name="inowas_layers_soil_profiles")
     **/
    private $soilProfileLayer;

    /**
     * Layer constructor.
     * @param User|null $owner
     * @param Project|null $project
     * @param bool|false $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);

        $this->soilProfileLayer = new ArrayCollection();
    }

    /**
     * Add soilProfileLayer
     *
     * @param \AppBundle\Entity\SoilProfileLayer $soilProfileLayer
     * @return Layer
     */
    public function addSoilProfileLayer(\AppBundle\Entity\SoilProfileLayer $soilProfileLayer)
    {
        $this->soilProfileLayer[] = $soilProfileLayer;

        return $this;
    }

    /**
     * Remove soilProfileLayer
     *
     * @param \AppBundle\Entity\SoilProfileLayer $soilProfileLayer
     */
    public function removeSoilProfileLayer(\AppBundle\Entity\SoilProfileLayer $soilProfileLayer)
    {
        $this->soilProfileLayer->removeElement($soilProfileLayer);
    }

    /**
     * Get soilProfileLayer
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSoilProfileLayer()
    {
        return $this->soilProfileLayer;
    }
}
