<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Grid\LayerNumber;

class BottomElevation extends AbstractSoilproperty
{

    public static function fromPointValue($value): BottomElevation
    {
        return new self($value);
    }


    public static function fromLayerValue($value): BottomElevation
    {
        return new self($value, true);
    }


    public static function fromLayerValueWithNumber($value, LayerNumber $layer): BottomElevation
    {
        return new self($value, true, $layer);
    }

    public static function fromArray(array $arr): BottomElevation
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
            return 'hbot';
    }
}
