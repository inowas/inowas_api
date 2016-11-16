<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

use Inowas\ModflowBundle\Model\StressPeriod;

class GhbStressPeriod extends StressPeriod
{
    /** @var float */
    private $stage;

    /** @var float */
    private $cond;

    /**
     * @return float
     */
    public function getStage(): float
    {
        return $this->stage;
    }

    /**
     * @param float $stage
     * @return GhbStressPeriod
     */
    public function setStage(float $stage): GhbStressPeriod
    {
        $this->stage = $stage;
        return $this;
    }

    /**
     * @return float
     */
    public function getCond(): float
    {
        return $this->cond;
    }

    /**
     * @param float $cond
     * @return GhbStressPeriod
     */
    public function setCond(float $cond): GhbStressPeriod
    {
        $this->cond = $cond;
        return $this;
    }

    /**
     * @param $value
     * @return GhbStressPeriod
     */
    public static function fromArray($value){
        $instance = new self();
        $instance->setDateTimeBegin(new \DateTime($value["dateTimeBegin"]));
        $instance->setDateTimeEnd(new \DateTime($value["dateTimeEnd"]));
        $instance->setNumberOfTimeSteps($value['numberOfTimeSteps']);
        $instance->setSteady($value['steady']);
        $instance->setTimeStepMultiplier($value['timeStepMultiplier']);
        $instance->setStage($value['stage']);
        $instance->setCond($value['cond']);
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();
        $arr['stage'] = $this->stage;
        $arr['cond'] = $this->cond;

        return $arr;
    }
}
