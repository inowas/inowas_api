<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as JMS;   

class TimeValue implements TimeValueInterface
{
    /**
     * @var \DateTime|null
     * @JMS\Groups({"list", "details"})
     */
    private $datetime;

    /**
     * @var float
     * @JMS\Type("float")
     * @JMS\Groups({"list", "details"})
     */
    private $value;

    /**
     * @return \DateTime|null
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * @param \DateTime|null $datetime
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }


}
