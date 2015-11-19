<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="layer")
 * @JMS\ExclusionPolicy("all")
 */
class Layer extends ModelObject
{
    /**
     * @var ArrayCollection SoilProfile
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalUnit", inversedBy="layer")
     * @ORM\JoinTable(name="layers_soil_profiles")
     **/
    private $geologicalUnits;

    /**
     * Layer constructor.
     * @param User|null $owner
     * @param Project|null $project
     * @param bool|false $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);

        $this->geologicalUnits = new ArrayCollection();
    }

    /**
     * Add geologicalUnit
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnit
     * @return Layer
     */
    public function addGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits[] = $geologicalUnit;

        return $this;
    }

    /**
     * Remove geologicalUnit
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnit
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits->removeElement($geologicalUnit);
    }

    /**
     * Get geologicalUnits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGeologicalUnits()
    {
        return $this->geologicalUnits;
    }
}
