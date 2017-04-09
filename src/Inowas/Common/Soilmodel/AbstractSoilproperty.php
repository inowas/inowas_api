<?php

namespace Inowas\Common\Soilmodel;

use Inowas\Soilmodel\Model\GeologicalLayerNumber;

abstract class AbstractSoilproperty
{

    /** @var array|float */
    protected $value;

    /** @var bool */
    protected $isLayer = false;

    abstract public static function create();
    abstract public static function fromPointValue($value);
    abstract public static function fromLayerValue($value);
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

    public function sameDimensionAs($value): bool
    {
        if (is_numeric($this->value) && is_numeric($value)){
            return true;
        }

        if ($this->is2DArray($this->value) && $this->is2DArray($value)) {
            if (count($this->value) === count($value) && count($this->value[0]) === count($value[0])){
                return true;
            }
        }

        return false;
    }

    public function is2DArray($value = null): bool
    {
        if (is_null($value)){
            $value = $this->value;
        }

        return (is_array($value) && is_array($value[0]) && !is_array($value[0][0]));
    }

    public function isNumeric($value = null): bool
    {
        if (is_null($value)){
            $value = $this->value;
        }

        return is_numeric($value);
    }
}
