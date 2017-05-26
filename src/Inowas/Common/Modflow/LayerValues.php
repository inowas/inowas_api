<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

final class LayerValues implements \JsonSerializable
{
    /** @var array */
    private $layerValues;

    public static function fromArray(array $layerValues): LayerValues
    {
        return new self($layerValues);
    }

    private function __construct(array $layerValues) {
        $this->layerValues = $layerValues;
    }

    public function toArray(): array
    {
        return $this->layerValues;
    }

    public function jsonSerialize()
    {
        return $this->layerValues;
    }
}

