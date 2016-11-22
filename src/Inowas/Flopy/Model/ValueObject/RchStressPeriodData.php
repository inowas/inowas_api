<?php

namespace Inowas\Flopy\Model\ValueObject;

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
    public function jsonSerialize()
    {
        return $this->rech->toReducedArray();
    }
}
