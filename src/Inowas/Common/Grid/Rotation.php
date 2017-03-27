<?php

declare(strict_types=1);

namespace Inowas\Common\Grid;

class Rotation
{
    /** @var float */
    private $rotation;

    public static function fromFloat(float $rotation): Rotation
    {
        return new self($rotation);
    }

    private function __construct(float $rotation)
    {
        $this->rotation = $rotation;
    }

    public function toFloat(): float
    {
        return $this->rotation;
    }
}
