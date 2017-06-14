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

    /**
     * @param string $resultType
     * @return ResultType
     */
    public static function fromString(string $resultType): ResultType
    {
        $self = new self();
        $self->type = $resultType;
        return $self;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function sameAs(ResultType $other): bool
    {
        return $this->toString() === $other->toString();
    }
}
