<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Ramsey\Uuid\Uuid;

class ModflowModelId
{
    /** @var  Uuid */
    private $uuid;

    public static function generate(): ModflowModelId
    {
        return new self(Uuid::uuid4());
    }

    public static function fromString(string $id): ModflowModelId
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

    public function sameValueAs(ModflowModelId $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
