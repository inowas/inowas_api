<?php
/**
 * continue : bool
 * if the model fails to converge during a time step then it will continue to
 * solve the following time step. (default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class NwtContinue
{
    /** @var bool */
    private $value;

    public static function fromBool(bool $value): NwtContinue
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toBool(): bool
    {
        return $this->value;
    }
}
