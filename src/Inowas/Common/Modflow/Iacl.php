<?php
/**
 * iacl : int
 * (XMD) is a flag for the acceleration method: 0 is conjugate gradient, 1 is ORTHOMIN,
 * 2 is Bi-CGSTAB. (default is 2).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Iacl
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Iacl
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
