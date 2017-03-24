<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

class GeologicalLayerDescription
{
    /** @var  string */
    private $description;

    public static function fromString(string $description): GeologicalLayerDescription
    {
        return new self($description);
    }

    private function __construct(string $description)
    {
        $this->description = $description;
    }

    public function toString(): string
    {
        return $this->description;
    }
}
