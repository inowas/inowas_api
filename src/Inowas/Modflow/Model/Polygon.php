<?php

namespace Inowas\Modflow\Model;

class Polygon
{
    /** @var array */
    private $ring = [];

    public static function fromArray(array $ring){
        return new self($ring);
    }

    private function __construct(array $ring)
    {
        $this->ring = $ring;
    }

    public function toArray()
    {
        return $this->ring;
    }
}
