<?php
/**
 * novfc : boolean
 * turns off the vertical flow correction under dewatered conditions.
 * This option turns off the vertical flow calculation described on p.
 * 5-8 of USGS Techniques and Methods Report 6-A16 and the vertical
 * conductance correction described on p. 5-18 of that report.
 * (default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Novfc
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): Novfc
    {
        return new self($value);
    }

    public static function fromValue(bool $value): Novfc
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
