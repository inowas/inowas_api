<?php
/*
 * Hani, if 2D
 * Chani if float
 */
declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class HydraulicAnisotropy extends AbstractSoilproperty
{

    const TYPE = 'hani';

    public static function create(): HydraulicAnisotropy
    {
        return new self(null);
    }

    public static function fromPointValue($value): HydraulicAnisotropy
    {
        return new self($value);
    }

    public static function fromLayerValue($value): HydraulicAnisotropy
    {
        return new self($value, true);
    }

    public static function fromArray(array $arr): HydraulicAnisotropy
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
