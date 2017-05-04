<?php
/**
 * levfill : int
 * (GMRES) is the fill limit for ILUMETHOD = 1 and is the level of fill for
 * ilumethod = 2. Recommended values: 5-10 for method 1, 0-2 for method 2.
 * (default is 5).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Levfill
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Levfill
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
