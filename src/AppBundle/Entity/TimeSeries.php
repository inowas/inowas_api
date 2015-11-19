<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TimeSeries
 *
 * @ORM\Table(name="time_series")
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
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Property", inversedBy="timeSeries")
     * @ORM\JoinColumn(name="model_object_properties_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $property;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeStamp", type="datetimetz", nullable=true)
     */
    private $timeStamp=null;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * @var string
     *
     * @ORM\Column(name="raster", type="string")
     */
    private $raster;

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
     * Set modelObjectProperties
     *
     * @param \AppBundle\Entity\Property $property
     * @return TimeSeries
     */
    public function setModelObjectProperties(Property $property = null)
    {
        $this->property = $property;
        return $this;
    }

    /**
     * Get modelObjectProperties
     *
     * @return \AppBundle\Entity\Property
     */
    public function getModelObjectProperties()
    {
        return $this->property;
    }

    /**
     * Set raster
     *
     * @param string $raster
     * @return TimeSeries
     */
    public function setRaster($raster)
    {
        $this->raster = $raster;

        return $this;
    }

    /**
     * Get raster
     *
     * @return string 
     */
    public function getRaster()
    {
        return $this->raster;
    }

    /**
     * Set property
     *
     * @param \AppBundle\Entity\Property $property
     * @return TimeSeries
     */
    public function setProperty(\AppBundle\Entity\Property $property = null)
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get property
     *
     * @return \AppBundle\Entity\Property 
     */
    public function getProperty()
    {
        return $this->property;
    }
}
