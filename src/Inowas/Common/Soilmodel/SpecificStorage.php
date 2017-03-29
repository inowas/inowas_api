<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Grid\LayerNumber;

final class SpecificStorage extends AbstractSoilproperty
{
    public static function fromFloat(float $value, ?LayerNumber $layer = null)
    {
        return new self($value, $layer);
    }

    public static function from2DArray(array $value, ?LayerNumber $layer = null)
    {
        return new self($value, $layer);
    }

    public static function fromValue($value, ?LayerNumber $layer = null)
    {
        return new self($value, $layer);
    }

    public function identifier(): string
    {
        return 'ss';
    }
}
