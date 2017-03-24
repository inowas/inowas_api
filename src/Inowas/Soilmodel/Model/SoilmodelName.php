<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

class SoilmodelName
{
    /** @var  string */
    private $name;

    public static function fromString(string $name): SoilmodelName
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
