<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;


class GeologicalLayerNumber
{
    /** @var  int */
    private $number;

    public static function fromInteger(int $number): GeologicalLayerNumber
    {
        return new self($number);
    }

    private function __construct(int $number)
    {
        $this->number = $number;
    }

    public function toInteger(): int
    {
        return $this->number;
    }
}
