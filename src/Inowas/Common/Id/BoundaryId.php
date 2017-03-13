<?php

declare(strict_types=1);

namespace Inowas\Common\Id;

use Ramsey\Uuid\Uuid;

class BoundaryId implements IdInterface
{
    /** @var  Uuid */
    private $uuid;

    public static function generate(): BoundaryId
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): BoundaryId
    {
        return new self(Uuid::fromString($id));
    }

    private function __construct(Uuid $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function sameValueAs(IdInterface $other): bool
    {
        return $this->toString() === $other->toString();
    }
}