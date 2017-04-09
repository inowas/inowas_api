<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

final class SpecificStorage extends AbstractSoilproperty
{

    const TYPE = 'ss';

    public static function create(): SpecificStorage
    {
        return new self(null);
    }

    public static function fromPointValue($value): SpecificStorage
    {
        return new self($value);
    }

    public static function fromLayerValue($value): SpecificStorage
    {
        return new self($value, true);
    }

    public static function fromArray(array $arr): SpecificStorage
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
