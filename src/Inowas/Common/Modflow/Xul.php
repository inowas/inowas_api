<?php
/**
 * xul : float
 * x coordinate of upper left corner of the grid, default is None
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Xul
{
    /** @var null|float */
    protected $value;

    public static function fromFloat(float $value): Xul
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromValue($value): Xul
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    private function __construct(){}

    public function toFloat(): float
    {
        return $this->value;
    }

    public function toValue(): ?float
    {
        return $this->value;
    }
}
