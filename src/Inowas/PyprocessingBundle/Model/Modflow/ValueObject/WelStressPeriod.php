<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use AppBundle\Model\StressPeriod;
use JMS\Serializer\Annotation as JMS;

class WelStressPeriod extends StressPeriod
{
    /**
     * @var float
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    private $flux;

    /**
     * @return float
     */
    public function getFlux(): float
    {
        return $this->flux;
    }

    /**
     * @param float $flux
     * @return WelStressPeriod
     */
    public function setFlux(float $flux): WelStressPeriod
    {
        $this->flux = $flux;
        return $this;
    }

    /**
     * @param $value
     * @return WelStressPeriod
     */
    public static function fromArray($value){
        $instance = new self();
        $instance->setDateTimeBegin(new \DateTime($value["dateTimeBegin"]));
        $instance->setDateTimeEnd(new \DateTime($value["dateTimeEnd"]));
        $instance->setNumberOfTimeSteps($value['numberOfTimeSteps']);
        $instance->setSteady($value['steady']);
        $instance->setTimeStepMultiplier($value['timeStepMultiplier']);
        $instance->setFlux($value['flux']);
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();
        $output['flux'] = $this->flux;

        return $output;
    }
}