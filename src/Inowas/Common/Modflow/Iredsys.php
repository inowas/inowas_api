<?php
/**
 * iredsys : int
 * (XMD) is a flag for reduced system preconditioning (integer): 0-do not apply
 * reduced system preconditioning, 1-apply reduced system preconditioning.
 * (default is 0)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iredsys
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Iredsys
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
