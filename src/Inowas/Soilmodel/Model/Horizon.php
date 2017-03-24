<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

class Horizon
{
    /** @var  HorizonId $id */
    protected $id;

    public function id(): HorizonId
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return array();
    }

    public static function fromArray(array $data): Horizon
    {
        $self = new self();
        return $self;
    }
}
