<?php
/**
 * constantcv : boolean
 * indicates that vertical conductance for an unconfined cell is
 * computed from the cell thickness rather than the saturated thickness.
 * The CONSTANTCV option automatically invokes the NOCVCORRECTION
 * option. (default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Constantcv
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): Constantcv
    {
        return new self($value);
    }

    public static function fromValue(bool $value): Constantcv
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
