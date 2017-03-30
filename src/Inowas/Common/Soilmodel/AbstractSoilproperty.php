<?php

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Grid\LayerNumber;

abstract class AbstractSoilproperty
{

    /** @var  float */
    protected $value;

    /** @var bool */
    protected $isLayer = false;

    abstract public static function fromPointValue($value);
    abstract public static function fromLayerValue($value);
    abstract public static function fromLayerValueWithNumber($value, LayerNumber $layer);
    abstract public static function fromArray(array $arr);
    abstract public function identifier(): string;

    protected function __construct($value, $isLayer = false, ?LayerNumber $layer= null)
    {
        if (is_null($layer)){
            $this->value = $value;
        }

        if ($layer instanceof LayerNumber){
            $this->value = [];
            $this->value[$layer->toInteger()]= $value;
        }

        $this->isLayer = $isLayer;
    }

    public function addLayerValue($value, LayerNumber $layer)
    {
        if (! is_array($this->value)) {
            $this->value = [];
        }

        $self = new $this($this->value, $this->isLayer);
        $self->value[$layer->toInteger()] = $value;
        return $self;
    }

    public function toValue()
    {
        return $this->value;
    }

    public function isLayerValue(): bool
    {
        return ($this->isLayer === true);
    }
}
