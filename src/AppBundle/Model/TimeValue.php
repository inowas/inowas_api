<?php

namespace AppBundle\Model;

class TimeValue implements TimeValueInterface
{
    /**
     * @var \DateTime|null
     */
    private $datetime;

    /**
     * @var float
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
