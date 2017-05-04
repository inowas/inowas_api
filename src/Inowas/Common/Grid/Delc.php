<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class Delc
{
    protected $delC;

    public static function fromArray(array $delC): Delc
    {
        $self = new self();
        $self->delC = $delC;
        return $self;
    }

    public static function fromValue($delC): Delc
    {
        $self = new self();
        $self->delC = $delC;
        return $self;
    }

    public function toValue()
    {
        return $this->delC;
    }

    public function isArray()
    {
        return is_array($this->delC);
    }
}
