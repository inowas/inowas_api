<?php

declare(strict_types=1);

namespace Inowas\Common\Id;


class CalculationId
{
    /** @var string */
    private $checksum;

    public static function fromString(string $id): CalculationId
    {
        return new self($id);
    }

    private function __construct(string $checksum)
    {
        $this->checksum = $checksum;
    }

    public function toString(): string
    {
        return $this->checksum;
    }

    public function sameValueAs($other): bool
    {
        if (!($other instanceof $this)) {
            return false;
        }

        return $this->toString() === $other->toString();
    }
}
