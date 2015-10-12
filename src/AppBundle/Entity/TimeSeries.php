<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TimeSeries
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class TimeSeries
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
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ModelObjectProperty", mappedBy="timeSeries")
     */
    private $modelObjectProperties;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeStamp", type="datetime", nullable=true)
     */
    private $timeStamp=null;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->modelObjectProperties = new ArrayCollection();
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
     * Set timeStamp
     *
     * @param \DateTime $timeStamp
     * @return TimeSeries
     */
    public function setTimeStamp($timeStamp)
    {
        $this->timeStamp = $timeStamp;

        return $this;
    }

    /**
     * Get timeStamp
     *
     * @return \DateTime 
     */
    public function getTimeStamp()
    {
        return $this->timeStamp;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return TimeSeries
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
     * Add modelObjectProperties
     *
     * @param \AppBundle\Entity\ModelObjectProperty $modelObjectProperties
     * @return TimeSeries
     */
    public function addModelObjectProperty(\AppBundle\Entity\ModelObjectProperty $modelObjectProperties)
    {
        $this->modelObjectProperties[] = $modelObjectProperties;

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
