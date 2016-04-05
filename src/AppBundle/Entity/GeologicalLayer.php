<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeologicalLayerRepository")
 * @ORM\Table(name="geological_layers")
 */
class GeologicalLayer extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails"})
     */
    protected $type = 'geologicallayer';

    /**
     * @var ArrayCollection SoilProfile
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalUnit", inversedBy="geologicalLayer")
     * @ORM\JoinTable(name="geological_layers_geological_units")
     * @JMS\Type("ArrayCollection<AppBundle\Entity\GeologicalUnit>"))
     * @JMS\Groups({"layerdetails"})
     **/
    private $geologicalUnits;

    /**
     * @var ArrayCollection Boundary
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Boundary", inversedBy="geologicalLayers")
     * @ORM\JoinTable(name="layers_boundaries")
     */
    private $boundaries;

    /**
     * Layer constructor.
     * @param User|null $owner
     * @param Project|null $project
     * @param bool|false $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);

        $this->boundaries = new ArrayCollection();
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

    /**
     * Add boundary
     *
     * @param \AppBundle\Entity\Boundary $boundary
     *
     * @return GeologicalLayer
     */
    public function addBoundary(\AppBundle\Entity\Boundary $boundary)
    {
        $this->boundaries[] = $boundary;

        return $this;
    }

    /**
     * Remove boundary
     *
     * @param \AppBundle\Entity\Boundary $boundary
     */
    public function removeBoundary(\AppBundle\Entity\Boundary $boundary)
    {
        $this->boundaries->removeElement($boundary);
    }

    /**
     * Get boundaries
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBoundaries()
    {
        return $this->boundaries;
    }
}
