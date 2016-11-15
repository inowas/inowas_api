<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

use AppBundle\Model\StressPeriod;

class RchStressPeriod extends StressPeriod
{
    /** @var  Flopy2DArray */
    private $rech;

    /**
     * @return Flopy2DArray
     */
    public function getRech(): Flopy2DArray
    {
        return $this->rech;
    }

    /**
     * @param Flopy2DArray $rech
     * @return RchStressPeriod
     */
    public function setRech(Flopy2DArray $rech): RchStressPeriod
    {
        $this->rech = $rech;
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
        $instance->setRech(Flopy2DArray::fromValue($value['rech']));
        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();
        $output['rech'] = $this->rech->toReducedArray();

        return $output;
    }
}
