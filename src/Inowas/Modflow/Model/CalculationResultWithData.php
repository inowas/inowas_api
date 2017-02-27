<?php

namespace Inowas\Modflow\Model;

class CalculationResultWithData implements \JsonSerializable
{

    /** @var  CalculationResultType */
    protected $type;

    /** @var  TotalTime */
    protected $totalTime;

    /** @var  LayerNumber */
    protected $layerNumber;

    /** @var  CalculationResultData */
    protected $data;

    public static function fromParameters(CalculationResultType $type, TotalTime $totalTime, LayerNumber $layerNumber, CalculationResultData $data): CalculationResultWithData
    {
        $self = new self();
        $self->type = $type;
        $self->totalTime = $totalTime;
        $self->layerNumber = $layerNumber;
        $self->data = $data;
        return $self;
    }

    public function type(): CalculationResultType
    {
        return $this->type;
    }

    public function totalTime(): TotalTime
    {
        return $this->totalTime;
    }

    public function layerNumber(): LayerNumber
    {
        return $this->layerNumber;
    }

    public function data(): CalculationResultData
    {
        return $this->data;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return array(
            'type' => $this->type->toString(),
            'layer' => $this->layerNumber->toInteger(),
            'totim' => $this->totalTime->toInteger(),
            'data' => $this->data->toArray()
        );
    }
}
