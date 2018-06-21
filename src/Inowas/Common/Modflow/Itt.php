<?php
/**
 * itt : int
 *      Flag that identifies whether head or head changes are used as
 *      observations. itt = 1 specified for heads and itt = 2 specified
 *      if initial value is head and subsequent changes in head. Only
 *      specified if irefsp is < 0. Default is 1.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Itt
{
    /** @var int */
    private $value;

    public static function fromValue(int $value = 1): Itt
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toInt(): int
    {
        return $this->value;
    }
}
