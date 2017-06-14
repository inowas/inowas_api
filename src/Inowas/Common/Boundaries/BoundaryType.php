<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

final class BoundaryType
{
    const CONSTANT_HEAD = "chd";
    const GENERAL_HEAD = "ghb";
    const RECHARGE = "rch";
    const RIVER = "riv";
    const WELL = "wel";

    /** @var  string */
    private $type;

    public static function fromString(string $type): BoundaryType
    {
        return new self($type);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function sameAs($type): bool
    {
        if ($type instanceof BoundaryType){
            return $this->toString() === $type->toString();
        }

        return false;
    }
}
