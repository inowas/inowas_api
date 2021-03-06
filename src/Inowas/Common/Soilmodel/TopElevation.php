<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

class TopElevation extends AbstractSoilproperty
{

    const TYPE = 'htop';

    public static function create(): TopElevation
    {
        return new self(null);
    }

    public static function fromPointValue($value): TopElevation
    {
        return new self($value);
    }


    public static function fromLayerValue($value): TopElevation
    {
        return new self($value, true);
    }

    public static function fromArray(array $arr): TopElevation
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
