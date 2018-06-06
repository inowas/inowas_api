<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Obsname
{
    /** @var  string */
    private $name;

    public static function fromString(string $name): Obsname
    {
        return new self($name);
    }

    public static function fromDefault(): Obsname
    {
        return new self('HOBS');
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
