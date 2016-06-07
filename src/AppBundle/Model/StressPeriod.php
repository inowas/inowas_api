<?php

namespace AppBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

class StressPeriod implements \JsonSerializable
{
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimeBegin", type="datetime")
     * @JMS\Groups({"modeldetails"})
     */
    private $dateTimeBegin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTimeEnd", type="datetime")
     * @JMS\Groups({"modeldetails"})
     */
    private $dateTimeEnd;

    /**
     * @var integer
     *
     * @ORM\Column(name="number_of_time_steps", type="integer")
     * @JMS\Groups({"modeldetails"})
     */
    private $numberOfTimeSteps;

    /**
     * StressPeriod constructor.
     * @param null $dateTimeBegin
     * @param null $dateTimeEnd
     * @param int $numberOfTimeSteps
     */
    public function __construct($dateTimeBegin = null, $dateTimeEnd = null, $numberOfTimeSteps = 1)
    {
        $this->dateTimeBegin = $dateTimeBegin;
        $this->dateTimeEnd = $dateTimeEnd;
        $this->numberOfTimeSteps = $numberOfTimeSteps;
    }

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

    /**
     * @return int
     */
    public function getNumberOfTimeSteps()
    {
        return $this->numberOfTimeSteps;
    }

    /**
     * @param int $numberOfTimeSteps
     */
    public function setNumberOfTimeSteps($numberOfTimeSteps)
    {
        $this->numberOfTimeSteps = $numberOfTimeSteps;
    }
}
