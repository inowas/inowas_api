<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\ValueObject;

class RchStressPeriodData implements \JsonSerializable
{
    /** @var Flopy2DArray */
    private $rech;

    private final function __construct(){}

    public static function create(Flopy2DArray $rech){
        $instance = new self();
        $instance->rech = $rech;

        return $instance;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->rech->toReducedArray();
    }
}
