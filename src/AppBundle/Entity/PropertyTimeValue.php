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
     */
    private $timeStamp;

    /**
     * @var float
     *
     * @ORM\Column(name="value", type="float")
     */
    private $value;

    /**
     * Set timeStamp
     *
     * @param \DateTime $timeStamp
     * @return PropertyTimeValue
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
}
