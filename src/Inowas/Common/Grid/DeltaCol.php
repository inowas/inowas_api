<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class DeltaCol
{
    protected $delC;

    public static function fromArray(array $delC): DeltaCol
    {
        $self = new self();
        $self->delC = $delC;
        return $self;
    }

    public static function fromValue($delC): DeltaCol
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
