<?php
/**
 * maxitinner : int
 * (GMRES) is the maximum number of iterations for the linear solution.
 * (default is 50).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Maxinner
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Maxinner
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
