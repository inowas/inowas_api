<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Id\IdInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class GeologicalLayerId implements IdInterface
{
    /** @var  Uuid */
    private $uuid;

    public static function generate(): GeologicalLayerId
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): GeologicalLayerId
    {
        return new self(Uuid::fromString($id));
    }

    private function __construct(UuidInterface $uuid)
    {
        $this->uuid = $uuid;
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function sameValueAs($other): bool
    {
        if (!$other instanceof IdInterface) {
            return false;
        }

        return $this->toString() === $other->toString();
    }
}
