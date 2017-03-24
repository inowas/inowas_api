<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

class GeologicalLayer
{
    /** @var  GeologicalLayerId */
    protected $id;

    /** @var  GeologicalLayerNumber */
    protected $number;

    /** @var  GeologicalLayerName */
    protected $name;

    /** @var  GeologicalLayerDescription */
    protected $description;

    public function id(): GeologicalLayerId
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'number' => $this->number->toInteger(),
            'name' => $this->name->toString(),
            'description' => $this->description->toString()
        );
    }

    public static function fromArray(array $layer): GeologicalLayer
    {
        $self = new self();
        $self->id = GeologicalLayerId::fromString($layer['id']);
        $self->number = GeologicalLayerNumber::fromInteger($layer['number']);
        $self->name = GeologicalLayerName::fromString($layer['name']);
        $self->description = GeologicalLayerName::fromString($layer['description']);
        return $self;
    }

    public static function fromParams(GeologicalLayerId $id, GeologicalLayerNumber $number, GeologicalLayerName $name, GeologicalLayerDescription $description): GeologicalLayer
    {
        $self = new self();
        $self->id = $id;
        $self->number = $number;
        $self->name = $name;
        $self->description = $description;
        return $self;
    }
}
