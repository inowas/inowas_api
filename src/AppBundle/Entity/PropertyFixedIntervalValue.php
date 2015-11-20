<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="property_fixed_interval_values")
 * @JMS\ExclusionPolicy("all")
 */
class PropertyFixedIntervalValue extends AbstractValue
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time", type="datetimetz")
     */
    private $dateTimeBegin;

    /**
     * @var String
     *
     * @ORM\Column(name="interval", type="string", length=255)
     */
    private $dateTimeInterval;

    /**
     * @var array
     *
     * @ORM\Column(name="values", type="simple_array")
     */
    private $values;

    /**
     * Set dateTimeBegin
     *
     * @param \DateTime $dateTimeBegin
     * @return PropertyFixedIntervalValue
     */
    public function setDateTimeBegin($dateTimeBegin)
    {
        $this->dateTimeBegin = $dateTimeBegin;

        return $this;
    }

    /**
     * Get dateTimeBegin
     *
     * @return \DateTime 
     */
    public function getDateTimeBegin()
    {
        return $this->dateTimeBegin;
    }

    /**
     * Set dateTimeInterval
     *
     * @param \DateInterval $dateTimeInterval
     * @return PropertyFixedIntervalValue
     */
    public function setDateTimeInterval(\DateInterval $dateTimeInterval)
    {
        $this->dateTimeInterval = $dateTimeInterval;

        return $this;
    }

    /**
     * Get dateTimeInterval
     *
     * @return \DateInterval
     */
    public function getDateTimeInterval()
    {
        return $this->dateTimeInterval;
    }

    /**
     * Set values
     *
     * @param array $values
     * @return PropertyFixedIntervalValue
     */
    public function setValues($values)
    {
        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return array 
     */
    public function getValues()
    {
        return $this->values;
    }
}
