<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

class WelStressPeriodData implements \JsonSerializable
{
    /** @var int */
    private $lay;

    /** @var int */
    private $col;

    /** @var int */
    private $row;

    /** @var float */
    private $flux;

    private final function __construct(){}

    public static function create(int $lay, int $row, int $col, float $flux){
        $instance = new self();
        $instance->lay = $lay;
        $instance->row = $row;
        $instance->col = $col;
        $instance->flux = $flux;

        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            $this->lay,
            $this->row,
            $this->col,
            $this->flux
        );
    }
}
