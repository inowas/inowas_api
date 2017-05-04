<?php
/**
 * nrchop : int
 * is the recharge option code.
 * 1: Recharge to top grid layer only
 * 2: Recharge to layer defined in irch
 * 3: Recharge to highest active cell (default is 3).
 *
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Nrchop
{
    /** @var int */
    private $value;

    public static function fromInteger(int $value): Nrchop
    {
        return new self($value);
    }

    public static function topGridLayerOnly(): Nrchop
    {
        return new self(1);
    }

    public static function layerDefinedInIrch(): Nrchop
    {
        return new self(2);
    }

    public static function highestActiveCell(): Nrchop
    {
        return new self(3);
    }

    public static function fromValue($value): Nrchop
    {
        return new self($value);
    }

    private function __construct(int $value)
    {
        $this->value = $value;
    }

    public function toValue(): int
    {
        return $this->value;
    }

    public function toInteger(): int
    {
        return $this->value;
    }
}
