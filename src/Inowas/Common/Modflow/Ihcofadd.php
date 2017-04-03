<?php
/**
 * ihcofadd : int
 * is a flag that determines what happens to an active cell that is
 * surrounded by dry cells.  (default is 0). If ihcofadd=0, cell
 * converts to dry regardless of HCOF value. This is the default, which
 * is the way PCG2 worked prior to the addition of this option. If
 * ihcofadd<>0, cell converts to dry only if HCOF has no head-dependent
 * stresses or storage terms.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ihcofadd
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Ihcofadd
    {
        return new self($value);
    }

    public static function fromValue(int $value): Ihcofadd
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

    public function toValue(): int
    {
        return $this->value;
    }
}
