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
     * @var boolean
     *
     * @ORM\Column(name="steady_state", type="boolean")
     * @JMS\Groups({"modeldetails"})
     */
    private $steady = true;

    /**
     * @var float
     *
     * @ORM\Column(name="timestep_multiplier", type="float")
     * @JMS\Groups({"modeldetails"})
     */
    private $timeStepMultiplier = 1.0;

    /**
     * StressPeriod constructor.
     * @param null $dateTimeBegin
     * @param null $dateTimeEnd
     * @param int $numberOfTimeSteps
     * @param bool $steady
     * @param float $timeStepMultiplier
     */
    public function __construct($dateTimeBegin = null, $dateTimeEnd = null, $numberOfTimeSteps = 1, $steady = true, $timeStepMultiplier = 1.0)
    {
        $this->dateTimeBegin = $dateTimeBegin;
        $this->dateTimeEnd = $dateTimeEnd;
        $this->numberOfTimeSteps = $numberOfTimeSteps;
        $this->steady = $steady;
        $this->timeStepMultiplier = $timeStepMultiplier;
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
     * @return float
     */
    public function getLengthInDays(){

        if (! $this->dateTimeBegin instanceof \DateTime){
            return null;
        }

        if (! $this->dateTimeEnd instanceof \DateTime){
            return null;
        }

        $dDiff = $this->dateTimeBegin->diff($this->dateTimeEnd);
        return $dDiff->days;
    }

    /**
     * @return int
     */
    public function getNumberOfTimeSteps()
    {
        return $this->numberOfTimeSteps;
    }

    /**
     * @param $numberOfTimeSteps
     * @return $this
     */
    public function setNumberOfTimeSteps($numberOfTimeSteps)
    {
        $this->numberOfTimeSteps = $numberOfTimeSteps;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isSteady()
    {
        return $this->steady;
    }

    /**
     * @param boolean $steady
     * @return $this
     */
    public function setSteady($steady)
    {
        $this->steady = $steady;
        return $this;
    }

    /**
     * @return float
     */
    public function getTimeStepMultiplier(): float
    {
        return $this->timeStepMultiplier;
    }

    /**
     * @param float $timeStepMultiplier
     * @return StressPeriod
     */
    public function setTimeStepMultiplier(float $timeStepMultiplier): StressPeriod
    {
        $this->timeStepMultiplier = $timeStepMultiplier;
        return $this;
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            "dateTimeBegin" => $this->dateTimeBegin,
            "dateTimeEnd" => $this->dateTimeEnd,
            "numberOfTimeSteps" => $this->numberOfTimeSteps,
            "steady" => $this->steady,
            "timeStepMultiplier" => $this->timeStepMultiplier
        );
    }
}