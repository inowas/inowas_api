<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

use Inowas\ModflowBundle\Model\StressPeriod;

class RivStressPeriod extends StressPeriod
{
    /**
     * @var float
     */
    private $stage;

    /**
     * @var float
     */
    private $cond;

    /**
     * @var float
     */
    private $rbot;

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
    public function getCond(): float
    {
        return $this->cond;
    }

    /**
     * @param float $cond
     * @return RivStressPeriod
     */
    public function setCond(float $cond): RivStressPeriod
    {
        $this->cond = $cond;
        return $this;
    }

    /**
     * @return float
     */
    public function getRbot(): float
    {
        return $this->rbot;
    }

    /**
     * @param float $rbot
     * @return RivStressPeriod
     */
    public function setRbot(float $rbot): RivStressPeriod
    {
        $this->rbot = $rbot;
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
        $instance->setCond($value['cond']);
        $instance->setRbot($value['rbot']);
        return $instance;
    }

    public function jsonSerialize()
    {
        $arr = parent::jsonSerialize();
        $arr['stage'] = $this->stage;
        $arr['cond'] = $this->cond;
        $arr['rbot'] = $this->rbot;

        return $arr;
    }
}
