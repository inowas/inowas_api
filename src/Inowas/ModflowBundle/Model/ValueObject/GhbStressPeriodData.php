<?php

namespace Inowas\ModflowBundle\Model\ValueObject;

class GhbStressPeriodData implements \JsonSerializable
{
    /** @var int */
    private $lay;

    /** @var int */
    private $col;

    /** @var int */
    private $row;

    /** @var float */
    private $stage;

    /** @var float */
    private $cond;

    private final function __construct(){}

    public static function create(int $lay, int $row, int $col, float $stage, float $cond){
        $instance = new self();
        $instance->lay = $lay;
        $instance->row = $row;
        $instance->col = $col;
        $instance->stage = $stage;
        $instance->cond = $cond;

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
            $this->stage,
            $this->cond
        );
    }
}
