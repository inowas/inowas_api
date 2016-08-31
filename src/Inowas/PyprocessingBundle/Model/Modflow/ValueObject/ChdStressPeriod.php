<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use AppBundle\Model\StressPeriod;

class ChdStressPeriod extends StressPeriod
{
    /** @var float */
    private $shead;

    /** @var float */
    private $ehead;

    /**
     * @return float
     */
    public function getShead(): float
    {
        return $this->shead;
    }

    /**
     * @param float $shead
     * @return ChdStressPeriod
     */
    public function setShead(float $shead): ChdStressPeriod
    {
        $this->shead = $shead;
        return $this;
    }

    /**
     * @return float
     */
    public function getEhead(): float
    {
        return $this->ehead;
    }

    /**
     * @param float $ehead
     * @return ChdStressPeriod
     */
    public function setEhead(float $ehead): ChdStressPeriod
    {
        $this->ehead = $ehead;
        return $this;
    }

    /**
     * @param $value
     * @return ChdStressPeriod
     */
    public static function fromArray($value){
        $instance = new self();
        $instance->setDateTimeBegin(new \DateTime($value["dateTimeBegin"]));
        $instance->setDateTimeEnd(new \DateTime($value["dateTimeEnd"]));
        $instance->setNumberOfTimeSteps($value['numberOfTimeSteps']);
        $instance->setSteady($value['steady']);
        $instance->setTimeStepMultiplier($value['timeStepMultiplier']);
        $instance->setShead($value['shead']);
        $instance->setEhead($value['ehead']);
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();
        $output['shead'] = $this->shead;
        $output['ehead'] = $this->ehead;

        return $output;
    }
}