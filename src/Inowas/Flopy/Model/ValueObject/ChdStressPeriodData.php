<?php

namespace Inowas\FlopyBundle\Model\ValueObject;

class ChdStressPeriodData implements \JsonSerializable
{
    /** @var int */
    private $lay;

    /** @var int */
    private $col;

    /** @var int */
    private $row;

    /** @var float */
    private $shead;

    /** @var float */
    private $ehead;

    private final function __construct(){}

    public static function create(int $lay, int $row, int $col, float $shead, float $ehead){
        $instance = new self();
        $instance->lay = $lay;
        $instance->row = $row;
        $instance->col = $col;
        $instance->shead = $shead;
        $instance->ehead = $ehead;

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
            $this->shead,
            $this->ehead
        );
    }
}
