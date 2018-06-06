<?php
/**
 * irefsp : int
 *      the stress period to which the observation time is referenced.
 *      (default is Null)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Irefsp
{
    /** @var int|null */
    private $value;

    public static function fromValue(?int $value = null): Irefsp
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toInteger(): ?int
    {
        return $this->value;
    }
}
