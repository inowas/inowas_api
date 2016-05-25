<?php

namespace AppBundle\Entity;

use AppBundle\Model\TimeValueFactory;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="property_time_values")
 */
class PropertyTimeValue extends AbstractValue
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeStamp", type="datetimetz")
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $datetime;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $value;

    /**
     * @var Raster $raster
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\Raster", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="raster_id", referencedColumnName="id", onDelete="SET NULL")
     * @JMS\Groups({"modeldetails", "modelobjectdetails"})
     */
    private $raster;

    /**
     * Set timeStamp
     *
     * @param \DateTime $datetime
     * @return PropertyTimeValue
     */
    public function setTimeStamp($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get timeStamp
     *
     * @return \DateTime 
     */
    public function getTimeStamp()
    {
        return $this->datetime;
    }

    /**
     * Set value
     *
     * @param float $value
     * @return PropertyTimeValue
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
     * Set datetime
     *
     * @param \DateTime $datetime
     * @return PropertyTimeValue
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;

        return $this;
    }

    /**
     * Get datetime
     *
     * @return \DateTime 
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    public function getDateBegin()
    {
        return $this->datetime;
    }

    public function getDateEnd()
    {
        return $this->datetime;
    }

    public function getNumberOfValues()
    {
        return 1;
    }

    public function getTimeValues()
    {
        return array(
            TimeValueFactory::create()
                ->setDatetime($this->getDateBegin())
                ->setValue($this->value)
                ->setRaster($this->raster)
        );
    }

    /**
     * Set raster
     *
     * @param \AppBundle\Entity\Raster $raster
     *
     * @return PropertyTimeValue
     */
    public function setRaster(\AppBundle\Entity\Raster $raster = null)
    {
        $this->raster = $raster;

        return $this;
    }

    /**
     * Get raster
     *
     * @return \AppBundle\Entity\Raster
     */
    public function getRaster()
    {
        return $this->raster;
    }
}
