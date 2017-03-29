<?php
/**
 * nocvcorrection : boolean
 * indicates that vertical conductance is not corrected when the vertical
 * flow correction is applied.
 * (default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class NoCvCorrection
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): NoCvCorrection
    {
        return new self($value);
    }

    public static function fromValue(bool $value): NoCvCorrection
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
