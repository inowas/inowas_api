<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class Proj4String
{
    /** @var string */
    private $proj4;

    public static function fromString(string $proj4): Proj4String
    {
        return new self($proj4);
    }

    private function __construct(string $proj4)
    {
        $this->proj4 = $proj4;
    }

    public function toString(): string
    {
        return $this->proj4;
    }
}
