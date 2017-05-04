<?php
/**
 * ibotavg : int
 * is a flag that indicates whether corrections will be made to groundwater
 * head relative to the cell-bottom altitude if the cell is surrounded by
 * dewatered cells (integer). A value of 1 indicates that a correction will
 * be made and a value of 0 indicates no correction will be made.
 * (default is 0).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Ibotavg
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Ibotavg
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
