<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class ListUnit
{

    /** @var  int */
    protected $listUnit;

    public static function fromInt(int $listUnit){
        $self = new self();
        $self->listUnit = $listUnit;
        return $self;
    }

    public function toInt(): int
    {
        return $this->listUnit;
    }
}
