<?php

declare(strict_types=1);

namespace Inowas\Common\Soilmodel;

use Inowas\Common\Soilmodel\HorizonId;

class Horizon
{
    /** @var HorizonId $id */
    protected $id;

    /** @var  HTop */
    protected $hTop;

    /** @var  HBottom */
    protected $hBot;

    /** @var  Conductivity */
    protected $conductivity;

    /** @var  Storage */
    protected $storage;

    /** @var  GeologicalLayerNumber */
    protected $layerNumber;

    public function id(): HorizonId
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'layer_number' => $this->layerNumber->toInteger(),
            'h_top' => $this->hTop->toMeters(),
            'h_bot' => $this->hBot->toMeters(),
            'conductivity' => $this->conductivity->toArray(),
            'storage' => $this->storage->toArray()
        );
    }

    public static function fromArray(array $data): Horizon
    {
        $self = new self();
        $self->id = HorizonId::fromString($data['id']);
        $self->layerNumber = GeologicalLayerNumber::fromInteger($data['layer_number']);
        $self->hTop = HTop::fromMeters($data['h_top']);
        $self->hBot = HBottom::fromMeters($data['h_bot']);
        $self->conductivity = Conductivity::fromArray($data['conductivity']);
        $self->storage = Storage::fromArray($data['storage']);
        return $self;
    }

    public static function fromParams(HorizonId $id, GeologicalLayerNumber $layerNumber, HTop $hTop, HBottom $hBot, Conductivity $cond, Storage $storage): Horizon
    {
        $self = new self();
        $self->id = $id;
        $self->layerNumber = $layerNumber;
        $self->hTop = $hTop;
        $self->hBot = $hBot;
        $self->conductivity = $cond;
        $self->storage = $storage;

        return $self;
    }

    public function hTop(): HTop
    {
        return $this->hTop;
    }

    public function hBot(): HBottom
    {
        return $this->hBot;
    }

    public function conductivity(): Conductivity
    {
        return $this->conductivity;
    }

    public function storage(): Storage
    {
        return $this->storage;
    }

    public function layerNumber(): GeologicalLayerNumber
    {
        return $this->layerNumber;
    }
}
