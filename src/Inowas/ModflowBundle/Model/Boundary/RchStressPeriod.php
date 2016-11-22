<?php

namespace Inowas\ModflowBundle\Model\Boundary;


class RchStressPeriod extends StressPeriod
{
    /** @var float */
    private $recharge;

    /**
     * @return float
     */
    public function getRecharge(): float {
        return $this->recharge;
    }

    /**
     * @param float $recharge
     * @return RchStressPeriod
     */
    public function setRecharge(float $recharge): RchStressPeriod
    {
        $this->recharge = $recharge;
        return $this;
    }

    /**
     * @param $value
     * @return RchStressPeriod
     */
    public static function fromArray($value): RchStressPeriod {
        $instance = new self();
        $instance->setDateTimeBegin(new \DateTime($value["dateTimeBegin"]));
        $instance->setNumberOfTimeSteps($value['numberOfTimeSteps']);
        $instance->setSteady($value['steady']);
        $instance->setTimeStepMultiplier($value['timeStepMultiplier']);
        $instance->setRecharge($value['rech']);
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize(): array {
        $output = parent::jsonSerialize();
        $output['rech'] = $this->recharge;
        return $output;
    }
}
