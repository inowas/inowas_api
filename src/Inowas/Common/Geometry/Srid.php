<?php

declare(strict_types=1);

namespace Inowas\Common\Geometry;

class Srid
{
    /** @var  integer */
    private $srid;

    public static function fromInt(int $srid): Srid
    {
        return new self($srid);
    }

    private function __construct(int $srid)
    {
        $this->srid = $srid;
    }

    public function toInteger(): int
    {
        return $this->srid;
    }
}
