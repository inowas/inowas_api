<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use Inowas\ModflowBundle\Model\StressPeriodInterface;
use Ramsey\Uuid\Uuid;

class StressPeriod implements StressPeriodInterface, \JsonSerializable
{
    /** @var  Uuid */
    private $id;

    /** @var \DateTime */
    private $dateTimeBegin;

    /** @var \DateTime */
    private $dateTimeEnd;

    /** @var integer */
    private $numberOfTimeSteps;

    /** @var boolean */
    private $steady = false;

    /** @var float  */
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
        $this->id = Uuid::uuid4();
        $this->dateTimeBegin = $dateTimeBegin;
        $this->dateTimeEnd = $dateTimeEnd;
        $this->numberOfTimeSteps = $numberOfTimeSteps;
        $this->steady = $steady;
        $this->timeStepMultiplier = $timeStepMultiplier;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @param \DateTime $dateTimeBegin
     * @return $this
     */
    public function setDateTimeBegin(\DateTime $dateTimeBegin)
    {
        $this->dateTimeBegin = $dateTimeBegin;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getDateTimeBegin()
    {
        return $this->dateTimeBegin;
    }

    /**
     * @param \DateTime $dateTimeEnd
     * @return $this
     */
    public function setDateTimeEnd(\DateTime $dateTimeEnd)
    {
        $this->dateTimeEnd = $dateTimeEnd;

        return $this;
    }

    /**
     * @return \DateTime|null
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
     * @return $this
     */
    public function setTimeStepMultiplier(float $timeStepMultiplier)
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
            "dateTimeBegin" => $this->dateTimeBegin->format(\DateTime::ATOM),
            "dateTimeEnd" => $this->dateTimeEnd->format(\DateTime::ATOM),
            "numberOfTimeSteps" => $this->numberOfTimeSteps,
            "steady" => $this->steady,
            "timeStepMultiplier" => $this->timeStepMultiplier
        );
    }
}
