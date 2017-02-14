<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class BoundaryName
{
    /** @var  string */
    private $name;

    public static function fromString(string $name): BoundaryName
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
