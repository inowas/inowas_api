<?php
/**
 * Package ModflowOc
 *
 * compact : boolean
 * Save results in compact budget form.
 * (default is True).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Compact
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): Compact
    {
        return new self($value);
    }

    private function __construct(bool $value)
    {
        $this->value = $value;
    }

    public function toBool(): bool
    {
        return $this->value;
    }
}
