<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

class ResultType
{
    const HEAD_TYPE = 'head';
    const DRAWDOWN_TYPE = 'drawdown';
    const BUDGET_TYPE = 'budget';

    /** @var  string */
    private $type;

    public static function fromString(string $type): ResultType
    {
        $self = new self();
        $self->type = $type;
        return $self;
    }

    public function toString(): string
    {
        return $this->type;
    }
}
