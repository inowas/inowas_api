<?php

namespace Inowas\ModflowBundle\Model\Boundary;

class WelStressPeriod extends StressPeriod
{
    /**
     * @var float
     */
    private $flux;

    /**
     * @return float
     */
    public function getFlux(): float {
        return $this->flux;
    }

    /**
     * @param float $flux
     * @return WelStressPeriod
     */
    public function setFlux(float $flux): WelStressPeriod {
        $this->flux = $flux;
        return $this;
    }

    /**
     * @param $value
     * @return WelStressPeriod
     */
    public static function fromArray($value): WelStressPeriod {
        $instance = new self();
        $instance->setDateTimeBegin(new \DateTime($value["dateTimeBegin"]));
        $instance->setNumberOfTimeSteps($value['numberOfTimeSteps']);
        $instance->setSteady($value['steady']);
        $instance->setTimeStepMultiplier($value['timeStepMultiplier']);
        $instance->setFlux($value['flux']);
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        $output = parent::jsonSerialize();
        $output['flux'] = $this->flux;
        return $output;
    }
}
