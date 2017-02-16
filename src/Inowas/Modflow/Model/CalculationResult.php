<?php

namespace Inowas\Modflow\Model;

class CalculationResult
{
    /** @var  TotalTime */
    protected $totalTime;

    /** @var  CalculationResultType */
    protected $type;

    /** @var  CalculationResultData */
    protected $data;

    public static function fromParameters(TotalTime $totalTime, CalculationResultType $type, CalculationResultData $data): CalculationResult
    {
        $self = new self();
        $self->totalTime = $totalTime;
        $self->type = $type;
        $self->data = $data;
        return $self;
    }

    public function totalTime(): TotalTime
    {
        return $this->totalTime;
    }

    public function type(): CalculationResultType
    {
        return $this->type;
    }

    public function data(): CalculationResultData
    {
        return $this->data;
    }
}
