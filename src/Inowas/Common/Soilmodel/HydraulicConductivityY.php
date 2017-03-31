<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Soilmodel\Model\GeologicalLayerNumber;

class HydraulicConductivityY extends AbstractSoilproperty
{

    public static function create(): HydraulicConductivityY
    {
        return new self(null);
    }

    public static function fromPointValue($value): HydraulicConductivityY
    {
        return new self($value);
    }

    public static function fromLayerValue($value): HydraulicConductivityY
    {
        return new self($value, true);
    }

    public static function fromLayerValueWithNumber($value, GeologicalLayerNumber $layer): HydraulicConductivityY
    {
        return new self($value, true, $layer);
    }

    public static function fromArray(array $arr): HydraulicConductivityY
    {
        return new self($arr['value'], $arr['is_layer']);
    }

    public function toArray(): array
    {
        return array(
            'value' => $this->value,
            'is_layer' => $this->isLayer
        );
    }

    public function identifier(): string
        {
            return 'ky';
    }
}
