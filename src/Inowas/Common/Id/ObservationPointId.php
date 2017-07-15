<?php

declare(strict_types=1);

namespace Inowas\Common\Id;

class ObservationPointId
{
    /** @var int */
    private $id;

    public static function fromInt(int $id): ObservationPointId
    {
        return new self($id);
    }

    private function __construct(int $id)
    {
        $this->id = $id;
    }

    public function toInt(): int
    {
        return $this->id;
    }

    public function sameValueAs(ObservationPointId $other): bool
    {
        return $this->toInt() === $other->toInt();
    }
}
