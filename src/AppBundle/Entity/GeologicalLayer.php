<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity
 * @ORM\Table(name="geological_layers")
 */
class GeologicalLayer extends ModelObject
{
    /**
     * @var string
     * @JMS\Groups({"list", "details"})
     */
    protected $type = 'geologicallayer';

    /**
     * @var ArrayCollection SoilProfile
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalUnit", inversedBy="geologicalLayer")
     * @ORM\JoinTable(name="geological_layers_geological_units")
     * @JMS\MaxDepth(2)
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
     * @return GeologicalLayer
     */
    public function addGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits[] = $geologicalUnit;

        if (!$geologicalUnit->getGeologicalLayer()->contains($this))
        {
            $geologicalUnit->addGeologicalLayer($this);
        }

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

        if ($geologicalUnit->getGeologicalLayer()->contains($this))
        {
            $geologicalUnit->removeGeologicalLayer($this);
        }
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
