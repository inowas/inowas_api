<?php
/**
 * storagecoefficient : boolean
 * indicates that variable Ss and SS parameters are read as storage
 * coefficient rather than specific storage. (default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Storagecoefficient
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): Storagecoefficient
    {
        return new self($value);
    }

    public static function fromValue(bool $value): Storagecoefficient
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

    public function toValue(): bool
    {
        return $this->value;
    }
}
