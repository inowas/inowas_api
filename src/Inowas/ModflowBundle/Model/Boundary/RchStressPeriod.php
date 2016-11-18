<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use Inowas\ModflowBundle\Model\ValueObject\Flopy2DArray;

class RchStressPeriod extends StressPeriod
{
    /** @var  Flopy2DArray */
    private $recharge;

    /**
     * @return Flopy2DArray
     */
    public function getRecharge(): Flopy2DArray
    {
        return $this->recharge;
    }

    /**
     * @param Flopy2DArray $recharge
     * @return RchStressPeriod
     */
    public function setRecharge(Flopy2DArray $recharge): RchStressPeriod
    {
        $this->recharge = $recharge;
        return $this;
    }

    /**
     * @param $value
     * @return RchStressPeriod
     */
    public static function fromArray($value){
        $instance = new self();
        $instance->setDateTimeBegin(new \DateTime($value["dateTimeBegin"]));
        $instance->setDateTimeEnd(new \DateTime($value["dateTimeEnd"]));
        $instance->setNumberOfTimeSteps($value['numberOfTimeSteps']);
        $instance->setSteady($value['steady']);
        $instance->setTimeStepMultiplier($value['timeStepMultiplier']);
        $instance->setRecharge(Flopy2DArray::fromValue($value['rech']));
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();
        $output['rech'] = $this->recharge->toReducedArray();

        return $output;
    }
}
