<?php
/**
 * options : list of strings
 * Package options.
 * (default is None).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Options
{
    /** @var null|array */
    private $value;

    public static function fromValue($value): Options
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toValue()
    {
        return $this->value;
    }
}
