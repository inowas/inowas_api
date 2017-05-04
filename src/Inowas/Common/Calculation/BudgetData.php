<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

class BudgetData implements \JsonSerializable
{
    /** @var  array */
    private $data;

    public static function fromArray(array $data): BudgetData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
