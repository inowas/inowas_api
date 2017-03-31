<?php

namespace Inowas\Common\Soilmodel;

use Inowas\Soilmodel\Model\GeologicalLayerNumber;

abstract class AbstractSoilproperty
{

    protected $value;

    /** @var bool */
    protected $isLayer = false;

    abstract public static function create();
    abstract public static function fromPointValue($value);
    abstract public static function fromLayerValue($value);
    abstract public static function fromLayerValueWithNumber($value, GeologicalLayerNumber $layer);
    abstract public static function fromArray(array $arr);
    abstract public function identifier(): string;

    protected function __construct($value, $isLayer = false, ?GeologicalLayerNumber $layer= null)
    {
        if (is_null($layer)){
            $this->value = $value;
        }

        if ($layer instanceof GeologicalLayerNumber){
            $this->value = [];
            $this->value[$layer->toInteger()]= $value;
        }

        $this->isLayer = $isLayer;
    }

    public function addLayerValue($value, GeologicalLayerNumber $layer)
    {
        if (! is_array($this->value)) {
            $this->value = [];
        }

        $this->value[$layer->toInteger()] = $value;
        return $this;
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
