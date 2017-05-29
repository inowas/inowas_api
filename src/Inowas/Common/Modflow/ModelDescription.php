<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class ModelDescription
{
    /** @var string */
    private $name;

    public static function fromString(string $name): ModelDescription
    {
        return new self($name);
    }

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
