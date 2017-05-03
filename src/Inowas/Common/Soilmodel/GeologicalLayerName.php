<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class GeologicalLayerName
{
    /** @var  string */
    private $name;

    public static function fromString(string $name): GeologicalLayerName
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
