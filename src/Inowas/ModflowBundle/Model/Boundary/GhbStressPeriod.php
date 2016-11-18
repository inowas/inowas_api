<?php

namespace Inowas\ModflowBundle\Model\Boundary;

class GhbStressPeriod extends StressPeriod
{
    /** @var float */
    private $stage;

    /** @var float */
    private $conductivity;

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
    public function getConductivity(): float
    {
        return $this->conductivity;
    }

    /**
     * @param float $conductivity
     * @return GhbStressPeriod
     */
    public function setConductivity(float $conductivity): GhbStressPeriod
    {
        $this->conductivity = $conductivity;
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
        $instance->setConductivity($value['cond']);
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();
        $arr['stage'] = $this->stage;
        $arr['cond'] = $this->conductivity;

        return $arr;
    }
}
