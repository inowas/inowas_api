<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

use Inowas\Common\Conductivity\LayerConductivity;
use Inowas\Common\Length\LayerHBottom;
use Inowas\Common\Length\LayerHTop;
use Inowas\Common\Storage\LayerStorage;

class GeologicalLayerValues
{
    /** @var LayerHBottom */
    private $hBottom;

    /** @var LayerHTop */
    private $hTop;

    /** @var LayerConductivity */
    private $conductivity;

    /** @var LayerStorage */
    private $storage;

    public static function fromParams(LayerHTop $hTop, LayerHBottom $hBot, LayerConductivity $conductivity, LayerStorage $storage): GeologicalLayerValues
    {
        $self = new self();
        $self->hTop = $hTop;
        $self->hBottom = $hBot;
        $self->conductivity = $conductivity;
        $self->storage = $storage;
        return $self;
    }

    public static function fromArray(array $data): GeologicalLayerValues
    {
        $self = new self();
        $self->hTop = LayerHTop::fromArray($data['h_top']);
        $self->hBottom = LayerHBottom::fromArray($data['h_bot']);
        $self->conductivity = LayerConductivity::fromArray($data['conductivity']);
        $self->storage = LayerStorage::fromArray($data['storage']);
        return $self;
    }

    public function toArray(): array
    {
        return array(
            'h_top' => $this->hTop->toArray(),
            'h_bot' => $this->hBottom->toArray(),
            'conductivity' => $this->conductivity->toArray(),
            'storage' => $this->storage->toArray()
        );
    }
}
