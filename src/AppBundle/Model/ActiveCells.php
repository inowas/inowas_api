<?php

namespace AppBundle\Model;

class ActiveCells
{
    private $activeCells;

    private final function __construct(){}

    public static function fromArray(array $activeCells)
    {
        $instance = new self();
        
        if (!is_array($activeCells[0])){
            throw new \InvalidArgumentException(sprintf(
                'ActiveCells is supposed to be an two dimensional array, %s given',
                gettype($activeCells)
            ));
        }

        $instance->activeCells = $activeCells;
        return $instance;
    }

    public function toArray()
    {
        return $this->activeCells;
    }
}