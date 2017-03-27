<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class DeltaRow
{
    protected $delR;

    public static function fromArray(array $delR): DeltaRow
    {
        $self = new self();
        $self->delR = $delR;
        return $self;
    }

    public static function fromValue($delR): DeltaRow
    {
        $self = new self();
        $self->delR = $delR;
        return $self;
    }

    public function toValue()
    {
        return $this->delR;
    }

    public function isArray()
    {
        return is_array($this->delR);
    }
}
