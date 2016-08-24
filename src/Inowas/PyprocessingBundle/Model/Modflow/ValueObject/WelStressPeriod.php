<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

use AppBundle\Model\StressPeriod;

class WelStressPeriod extends StressPeriod
{
    /** @var float */
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
     * @return array
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();
        $output['flux'] = $this->flux;

        return $output;
    }
}