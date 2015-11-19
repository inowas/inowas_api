<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * FeatureProperty
 *
 * @ORM\Table(name="model_object_property")
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
     * @var ModelObject
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModelObject", inversedBy="modelObjectProperties")
     * @ORM\JoinColumn(name="model_object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $modelObject;

    /**
     * @var ModelObjectPropertyType
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModelObjectPropertyType")
     * @ORM\JoinColumn(name="model_object_property_type_id", referencedColumnName="id")
     */
    private $type;

    /**
     * @var ArrayCollection TimeSeries
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\TimeSeries", mappedBy="modelObjectProperties")
     */
    private $timeSeries;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->timeSeries = new ArrayCollection();
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
     * Add timeSeries
     *
     * @param \AppBundle\Entity\TimeSeries $timeSeries
     * @return ModelObjectProperty
     */
    public function addTimeSeries(TimeSeries $timeSeries)
    {
        $this->timeSeries[] = $timeSeries;

        return $this;
    }

    /**
     * Remove timeSeries
     *
     * @param \AppBundle\Entity\TimeSeries $timeSeries
     */
    public function removeTimeSeries(TimeSeries $timeSeries)
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
     * Set type
     *
     * @param \AppBundle\Entity\ModelObjectPropertyType $type
     * @return ModelObjectProperty
     */
    public function setType(\AppBundle\Entity\ModelObjectPropertyType $type = null)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return \AppBundle\Entity\ModelObjectPropertyType 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set modelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return ModelObjectProperty
     */
    public function setModelObject(\AppBundle\Entity\ModelObject $modelObject = null)
    {
        $this->modelObject = $modelObject;

        return $this;
    }

    /**
     * Get modelObject
     *
     * @return \AppBundle\Entity\ModelObject 
     */
    public function getModelObject()
    {
        return $this->modelObject;
    }
}
