<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Hobname
{
    /** @var  string */
    private $name;

    public static function fromString(string $name): Hobname
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

    public function sameAs(Hobname $object): bool
    {
        return $this->toString() === $object->toString();
    }
}
