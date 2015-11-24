<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="property_time_values")
 * @JMS\ExclusionPolicy("all")
 */
class PropertyTimeValue extends AbstractValue
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timeStamp", type="datetimetz")
     * @JMS\Expose()
     */
    private $datetime;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     * @JMS\Expose()
     */
    private $value;

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
}
