<?php
/**
 * north : int
 * (XMD) is the number of orthogonalization for the ORTHOMIN acceleration scheme.
 * A number between 4 and 10 is appropriate. Small values require less storage
 * but more iterations may be required. This number should equal 2 for the other
 * acceleration methods. (default is 7).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class North
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): North
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
