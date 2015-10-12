<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureProperty
 *
 * @ORM\Table(name="inowas_model_object_property")
 * @ORM\Entity
 */
class ModelObjectProperty
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
     * @var ArrayCollection ModelObject
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ModelObject", mappedBy="modelObjectProperties")
     */
    private $modelObjects;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name = "";

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float", nullable=true)
     */
    private $value = null;

    /**
     * @var ArrayCollection TimeSeries
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TimeSeries", mappedBy="modelObjectProperty")
     */
    private $timeSeries;

    /**
     * @var ArrayCollection Raster
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\Raster", inversedBy="modelObjectProperty")
     * @ORM\JoinTable(name="inowas_model_object_properties_raster")
     */
    private $raster;
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelObjects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->timeSeries = new \Doctrine\Common\Collections\ArrayCollection();
        $this->raster = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set name
     *
     * @param string $name
     * @return ModelObjectProperty
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return ModelObjectProperty
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Add modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     * @return ModelObjectProperty
     */
    public function addModelObject(\AppBundle\Entity\ModelObject $modelObjects)
    {
        $this->modelObjects[] = $modelObjects;

        return $this;
    }

    /**
     * Remove modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     */
    public function removeModelObject(\AppBundle\Entity\ModelObject $modelObjects)
    {
        $this->modelObjects->removeElement($modelObjects);
    }

    /**
     * Get modelObjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModelObjects()
    {
        return $this->modelObjects;
    }

    /**
     * Add timeSeries
     *
     * @param \AppBundle\Entity\TimeSeries $timeSeries
     * @return ModelObjectProperty
     */
    public function addTimeSeries(\AppBundle\Entity\TimeSeries $timeSeries)
    {
        $this->timeSeries[] = $timeSeries;

        return $this;
    }

    /**
     * Remove timeSeries
     *
     * @param \AppBundle\Entity\TimeSeries $timeSeries
     */
    public function removeTimeSeries(\AppBundle\Entity\TimeSeries $timeSeries)
    {
        $this->timeSeries->removeElement($timeSeries);
    }

    /**
     * Get timeSeries
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getTimeSeries()
    {
        return $this->timeSeries;
    }

    /**
     * Add raster
     *
     * @param \AppBundle\Entity\Raster $raster
     * @return ModelObjectProperty
     */
    public function addRaster(\AppBundle\Entity\Raster $raster)
    {
        $this->raster[] = $raster;

        return $this;
    }

    /**
     * Remove raster
     *
     * @param \AppBundle\Entity\Raster $raster
     */
    public function removeRaster(\AppBundle\Entity\Raster $raster)
    {
        $this->raster->removeElement($raster);
    }

    /**
     * Get raster
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRaster()
    {
        return $this->raster;
    }
}
