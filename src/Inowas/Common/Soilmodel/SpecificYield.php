<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Soilmodel\Model\GeologicalLayerNumber;

final class SpecificYield extends AbstractSoilproperty
{

    public static function create(): SpecificYield
    {
        return new self(null);
    }

    public static function fromPointValue($value): SpecificYield
    {
        return new self($value);
    }

    public static function fromLayerValue($value): SpecificYield
    {
        return new self($value, true);
    }

    public static function fromLayerValueWithNumber($value, GeologicalLayerNumber $layer): SpecificYield
    {
        return new self($value, true, $layer);
    }

    public static function fromArray(array $arr): SpecificYield
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
        return 'sy';
    }
}
