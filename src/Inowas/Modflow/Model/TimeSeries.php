<?php

namespace Inowas\Modflow\Model;

class TimeSeries implements \JsonSerializable
{

    /** @var  CalculationResultType */
    protected $type;

    /** @var  LayerNumber */
    protected $layer;

    /** @var  RowNumber */
    protected $row;

    /** @var  ColumnNumber */
    private $column;

    /** @var  TimeSeriesData */
    protected $data;

    public static function fromParameters(
        CalculationResultType $type,
        LayerNumber $layerNumber,
        RowNumber $row,
        ColumnNumber $column,
        TimeSeriesData $data
    ): TimeSeries
    {
        $self = new self();
        $self->type = $type;
        $self->layer = $layerNumber;
        $self->row = $row;
        $self->column = $column;
        $self->data = $data;
        return $self;
    }

    public function type(): CalculationResultType
    {
        return $this->type;
    }

    public function layer(): LayerNumber
    {
        return $this->layer;
    }

    public function row(): RowNumber
    {
        return $this->row;
    }

    public function column(): ColumnNumber
    {
        return $this->column;
    }

    public function data(): TimeSeriesData
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
            'layer' => $this->layer->toInteger(),
            'row' => $this->row->toInteger(),
            'column' => $this->column->toInteger(),
            'data' => $this->data->toArray()
        );
    }
}
