<?php

namespace Inowas\ModflowBundle\Model\Boundary;

class RivStressPeriod extends StressPeriod
{
    /**
     * @var float
     */
    private $stage;

    /**
     * @var float
     */
    private $conductivity;

    /**
     * @var float
     */
    private $bottomElevation;

    /**
     * @return float
     */
    public function getStage(): float
    {
        return $this->stage;
    }

    /**
     * @param float $stage
     * @return RivStressPeriod
     */
    public function setStage(float $stage): RivStressPeriod
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
     * @return RivStressPeriod
     */
    public function setConductivity(float $conductivity): RivStressPeriod
    {
        $this->conductivity = $conductivity;
        return $this;
    }

    /**
     * @return float
     */
    public function getBottomElevation(): float
    {
        return $this->bottomElevation;
    }

    /**
     * @param float $bottomElevation
     * @return RivStressPeriod
     */
    public function setBottomElevation(float $bottomElevation): RivStressPeriod
    {
        $this->bottomElevation = $bottomElevation;
        return $this;
    }

    /**
     * @param $value
     * @return RivStressPeriod
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
        $instance->setBottomElevation($value['rbot']);
        return $instance;
    }

    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();
        $arr['stage'] = $this->stage;
        $arr['cond'] = $this->conductivity;
        $arr['rbot'] = $this->bottomElevation;

        return $arr;
    }
}
