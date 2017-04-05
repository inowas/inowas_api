<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class ObservationPointName
{

    /** @var  string */
    protected $name;

    public static function fromString(string $name): ObservationPointName
    {
        return new self($name);
    }

    private function __construct($name)
    {
        $this->name = $name;
    }

    public function toString()
    {
        return $this->name;
    }

    public function sameAs(ObservationPointName $name): bool
    {
        return $this->name == $name->toString();
    }
}
