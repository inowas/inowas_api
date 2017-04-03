<?php
/**
 * layavg : int or array of ints (nlay)
 * Layer average (default is 0).
 * 0 is harmonic mean
 * 1 is logarithmic mean
 * 2 is arithmetic mean of saturated thickness and logarithmic mean of hydraulic conductivity
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;


class Layavg
{

    const TYPE_HARMONIC_MEAN = 0;
    const TYPE_LOGARITHMIC_MEAN = 1;
    const TYPE_ARITHMETIC_MEAN = 2;

    /** @var  */
    private $value;

    private function __construct()
    {}

    public static function fromArray(array $value): Layavg
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromInt(int $value): Layavg
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public static function fromValue($value): Layavg
    {
        $self = new self();
        $self->value = $value;
        return $self;
    }

    public function toArray(): array
    {
        return $this->value;
    }

    public function toInt(): int
    {
        return $this->value;
    }

    public function toValue()
    {
        return $this->value;
    }

    public function isArray(): bool
    {
        return is_array($this->value);
    }
}
