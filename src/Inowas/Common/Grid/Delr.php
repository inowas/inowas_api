<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class Delr
{
    protected $delR;

    public static function fromArray(array $delR): Delr
    {
        $self = new self();
        $self->delR = $delR;
        return $self;
    }

    public static function fromValue($delR): Delr
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
