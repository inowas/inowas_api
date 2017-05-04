<?php
/**
 * idroptol : int
 * (XMD) is a flag for using drop tolerance in the preconditioning: 0-don't
 * use drop tolerance, 1-use drop tolerance. (default is 1).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Idroptol
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Idroptol
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toInteger(): int
    {
        return $this->value;
    }
}
