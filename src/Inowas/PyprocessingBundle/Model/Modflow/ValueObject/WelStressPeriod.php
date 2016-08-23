<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use AppBundle\Model\StressPeriod;

class WelStressPeriod extends StressPeriod
{
    /** @var float */
    private $pumpingRate;

    /**
     * @return float
     */
    public function getPumpingRate(): float
    {
        return $this->pumpingRate;
    }

    /**
     * @param float $pumpingRate
     * @return WelStressPeriod
     */
    public function setPumpingRate(float $pumpingRate): WelStressPeriod
    {
        $this->pumpingRate = $pumpingRate;
        return $this;
    }
}