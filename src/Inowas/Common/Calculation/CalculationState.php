<?php

declare(strict_types=1);

namespace Inowas\Common\Calculation;

class BudgetType
{
    const CUMULATIVE_BUDGET = 'cumulative';
    const INCREMENTAL_BUDGET = 'incremental';

    /** @var  string */
    private $type;

    public static function fromString(string $type): BudgetType
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
