<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="soil_profile")
 * @JMS\ExclusionPolicy("all")
 */
class SoilProfile extends ModelObject
{
    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $point;

    /**
     * @var ArrayCollection SoilProfileLayer $soilProfileLayers
     *
     * @ORM\OneToMany(targetEntity="SoilProfileLayer", mappedBy="soilProfile")
     */
    private $soilProfileLayers;

    /**
     * SoilProfile constructor.
     * @param User|null $owner
     * @param Project|null $project
     * @param bool|false $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);
        $this->soilProfileLayers = new ArrayCollection();
    }

    /**
     * Set point
     *
     * @param point $point
     * @return SoilProfile
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return point
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Add soilProfileLayers
     *
     * @param \AppBundle\Entity\SoilProfileLayer $soilProfileLayers
     * @return SoilProfile
     */
    public function addSoilProfileLayer(SoilProfileLayer $soilProfileLayers)
    {
        $this->soilProfileLayers[] = $soilProfileLayers;

        return $this;
    }

    /**
     * Remove soilProfileLayers
     *
     * @param \AppBundle\Entity\SoilProfileLayer $soilProfileLayers
     */
    public function removeSoilProfileLayer(SoilProfileLayer $soilProfileLayers)
    {
        $this->soilProfileLayers->removeElement($soilProfileLayers);
    }

    /**
     * Get soilProfileLayers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSoilProfileLayers()
    {
        return $this->soilProfileLayers;
    }
}
