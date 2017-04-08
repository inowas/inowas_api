<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

use Inowas\Common\Soilmodel\AbstractSoilproperty;
use Inowas\Common\Soilmodel\BottomElevation;
use Inowas\Common\Soilmodel\Conductivity;
use Inowas\Common\Soilmodel\SpecificStorage;
use Inowas\Common\Soilmodel\SpecificYield;
use Inowas\Common\Soilmodel\TopElevation;
use Inowas\Common\Soilmodel\Storage;
use Inowas\Soilmodel\Model\Exception\PropertyNotFoundException;

class GeologicalLayerValues
{
    /** @var BottomElevation */
    private $hBottom;

    /** @var TopElevation */
    private $hTop;

    /** @var Conductivity */
    private $conductivity;

    /** @var Storage */
    private $storage;


    public function hBottom(): BottomElevation
    {
        return $this->hBottom;
    }

    public function hTop(): TopElevation
    {
        return $this->hTop;
    }

    public function conductivity(): Conductivity
    {
        return $this->conductivity;
    }

    public function storage(): Storage
    {
        return $this->storage;
    }

    public static function fromParams(TopElevation $hTop, BottomElevation $hBot, Conductivity $conductivity, Storage $storage): GeologicalLayerValues
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
        $self->hTop = TopElevation::fromArray($data['h_top']);
        $self->hBottom = BottomElevation::fromArray($data['h_bot']);
        $self->conductivity = Conductivity::fromArray($data['conductivity']);
        $self->storage = Storage::fromArray($data['storage']);
        return $self;
    }

    public static function fromDefault(): GeologicalLayerValues
    {
        $self = new self();
        $self->hTop = TopElevation::fromLayerValue(1);
        $self->hBottom = BottomElevation::fromLayerValue(1);
        $self->conductivity = Conductivity::fromDefault();
        $self->storage = Storage::fromDefault();
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

    public function updateProperty(AbstractSoilproperty $property): GeologicalLayerValues
    {
        if ($property instanceof TopElevation){
            return $this->updateHTop($property);
        }

        if ($property instanceof BottomElevation){
            return $this->updateHBot($property);
        }

        if ($property instanceof SpecificStorage){
            return $this->updateSs($property);
        }

        if ($property instanceof SpecificYield){
            return $this->updateSy($property);
        }

        throw PropertyNotFoundException::withIdentifier($property->identifier());
    }

    private function updateHTop(TopElevation $htop): GeologicalLayerValues
    {
        $self = GeologicalLayerValues::fromArray($this->toArray());
        $self->hTop = $htop;
        return $self;
    }

    private function updateHBot(BottomElevation $hbot): GeologicalLayerValues
    {
        $self = GeologicalLayerValues::fromArray($this->toArray());
        $self->hBottom = $hbot;
        return $self;
    }

    private function updateSs(SpecificStorage $ss): GeologicalLayerValues
    {
        $self = GeologicalLayerValues::fromArray($this->toArray());
        $self->storage = $this->storage()->updateSs($ss);
        return $self;
    }

    private function updateSy(SpecificYield $sy): GeologicalLayerValues
    {
        $self = GeologicalLayerValues::fromArray($this->toArray());
        $self->storage = $this->storage()->updateSy($sy);
        return $self;
    }
}
