<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Id\IdInterface;
use Ramsey\Uuid\Uuid;

class HorizonId implements IdInterface
{
    /** @var  Uuid */
    private $uuid;

    public static function generate(): HorizonId
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): HorizonId
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
