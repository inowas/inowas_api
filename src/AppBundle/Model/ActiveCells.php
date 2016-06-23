<?php

namespace AppBundle\Model;

class ActiveCells
{
    private $activeCells;

    private final function __construct(){}

    public function fromArray(array $activeCells)
    {
        if (! is_array($activeCells)){
            throw new \InvalidArgumentException(sprintf(
                'ActiveCells is supposed to be an array, %s given',
                $activeCells
            ));
        }

        $this->activeCells = $activeCells;
        return $this;
    }

    public function getActiveCells(){
        return $this->activeCells;
    }
}