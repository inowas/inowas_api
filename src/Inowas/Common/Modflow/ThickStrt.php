<?php
/**
 * thickstrt : boolean
 * indicates that layers having a negative LAYTYP are confined, and their
 * cell thickness for conductance calculations will be computed as
 * STRT-BOT rather than TOP-BOT. (default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class ThickStrt
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): ThickStrt
    {
        return new self($value);
    }

    public static function fromValue(bool $value): ThickStrt
    {
        return new self($value);
    }

    private function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function toBool(): bool
    {
        return $this->value;
    }

    public function toValue(): bool
    {
        return $this->value;
    }
}
