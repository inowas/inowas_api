<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class HydraulicConductivityX extends AbstractSoilproperty
{

    const TYPE = 'kx';

    public static function create(): HydraulicConductivityX
    {
        return new self(null);
    }

    public static function fromPointValue($value): HydraulicConductivityX
    {
        return new self($value);
    }

    public static function fromLayerValue($value): HydraulicConductivityX
    {
        return new self($value, true);
    }

    public static function fromArray(array $arr): HydraulicConductivityX
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
            return self::TYPE;
    }
}
