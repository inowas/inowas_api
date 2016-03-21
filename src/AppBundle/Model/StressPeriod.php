<?php

namespace AppBundle\Model;

use Doctrine\ORM\Mapping as ORM;

class StressPeriod implements \JsonSerializable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimeBegin", type="datetime")
     */
    private $dateTimeBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimeEnd", type="datetime")
     */
    private $dateTimeEnd;

    /**
     * Set dateTimeBegin
     *
     * @param \DateTime $dateTimeBegin
     *
     * @return stressPeriod
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
     * Set dateTimeEnd
     *
     * @param \DateTime $dateTimeEnd
     *
     * @return stressPeriod
     */
    public function setDateTimeEnd($dateTimeEnd)
    {
        $this->dateTimeEnd = $dateTimeEnd;

        return $this;
    }

    /**
     * Get dateTimeEnd
     *
     * @return \DateTime
     */
    public function getDateTimeEnd()
    {
        return $this->dateTimeEnd;
    }

    /**
     * @return object
     */
    function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }


}
