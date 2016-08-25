<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

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
     * @return array
     */
    public function jsonSerialize()
    {
        $output = parent::jsonSerialize();
        $output['rech'] = $this->rech->toReducedArray();

        return $output;
    }
}