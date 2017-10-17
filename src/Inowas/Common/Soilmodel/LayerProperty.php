<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class LayerProperty
{
    /** @var string */
    private $property;

    public static function fromString(string $property): LayerProperty
    {
        return new self($property);
    }

    private function __construct(string $property)
    {
        $this->property = $property;
    }

    public function toString(): string
    {
        return $this->property;
    }

    public function sameValueAs(LayerProperty $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
