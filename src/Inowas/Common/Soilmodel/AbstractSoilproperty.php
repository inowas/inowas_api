<?php

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Grid\LayerNumber;

abstract class AbstractSoilproperty implements \JsonSerializable
{

    /** @var  float|array */
    protected $value;

    /** @var bool */
    private $isLayer = false;

    abstract public static function fromFloat(float $value, ?LayerNumber $layer = null);
    abstract public static function from2DArray(array $value, ?LayerNumber $layer = null);
    abstract public static function fromValue($value, ?LayerNumber $layer = null);
    abstract public function identifier(): string;

    protected function __construct($value, $layer)
    {
        if ($layer instanceof LayerNumber){
            $this->isLayer = true;
            $this->value[$layer->toInteger()] = $value;
        } else {
            $this->value = $value;
        }
    }

    public function addLayerValue($value, LayerNumber $layer)
    {
        $this->value[$layer->toInteger()] = $value;
        return json_decode(json_encode($this));
    }

    public function toValue()
    {
        return $this->value;
    }

    public function isArray(): bool
    {
        return is_array($this->value);
    }

    public function isLayerValue(): bool
    {
        return ($this->isLayer === true);
    }

    public function jsonSerialize()
    {
        return array(
            'value' => $this->value,
            'is_layer' => $this->isLayer
        );
    }
}
