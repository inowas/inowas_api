<?php

namespace Inowas\Modflow\Model;

class CalculationResultType
{
    const HEAD_TYPE = 'head';
    const DRAWDOWN_TYPE = 'drawdown';
    const WATER_TABLE_TYPE = 'water_table';

    /** @var  string */
    private $type;

    public static function fromString(string $type): CalculationResultType
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
