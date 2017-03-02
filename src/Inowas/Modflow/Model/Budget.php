<?php

namespace Inowas\Modflow\Model;

class Budget
{
    /** @var  array */
    private $data;

    public static function fromArray(array $data): Budget
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function toArray(): array
    {
        return $this->data;
    }
}
