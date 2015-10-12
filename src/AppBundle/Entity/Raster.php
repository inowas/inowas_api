<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Raster
 *
 * @ORM\Table(name="inowas_raster")
 * @ORM\Entity
 */
class Raster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var ArrayCollection ModelObjectProperty
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ModelObjectProperty", mappedBy="raster")
     */
    private $modelObjectProperties;

    /**
     * @ORM\Column(name="rast", type="text")
     */
    private $rast;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelObjectProperties = new \Doctrine\Common\Collections\ArrayCollection();
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

    /**
     * Set rast
     *
     * @param string $rast
     * @return Raster
     */
    public function setRast($rast)
    {
        $this->rast = $rast;

        return $this;
    }

    /**
     * Get rast
     *
     * @return string 
     */
    public function getRast()
    {
        return $this->rast;
    }

    /**
     * Add modelObjectProperties
     *
     * @param \AppBundle\Entity\ModelObjectProperty $modelObjectProperties
     * @return Raster
     */
    public function addModelObjectProperty(\AppBundle\Entity\ModelObjectProperty $modelObjectProperties)
    {
        $this->modelObjectProperties[] = $modelObjectProperties;
        $modelObjectProperties->addRaster($this);
        return $this;
    }

    /**
     * Remove modelObjectProperties
     *
     * @param \AppBundle\Entity\ModelObjectProperty $modelObjectProperties
     */
    public function removeModelObjectProperty(\AppBundle\Entity\ModelObjectProperty $modelObjectProperties)
    {
        $this->modelObjectProperties->removeElement($modelObjectProperties);
        $modelObjectProperties->removeRaster($this);
    }

    /**
     * Get modelObjectProperties
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModelObjectProperties()
    {
        return $this->modelObjectProperties;
    }
}
