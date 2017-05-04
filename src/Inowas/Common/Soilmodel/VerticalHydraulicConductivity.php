<?php
/*
 * Vka, if 2D
 * LayVka if float
 */
declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class VerticalHydraulicConductivity extends AbstractSoilproperty
{

    const TYPE = 'vka';

    public static function create(): VerticalHydraulicConductivity
    {
        return new self(null);
    }

    public static function fromPointValue($value): VerticalHydraulicConductivity
    {
        return new self($value);
    }

    public static function fromLayerValue($value): VerticalHydraulicConductivity
    {
        return new self($value, true);
    }

    public static function fromArray(array $arr): VerticalHydraulicConductivity
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
