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

    /** @var  GeologicalLayerValues */
    protected $values;

    public function id(): GeologicalLayerId
    {
        return $this->id;
    }

    public function toArray(): array
    {
        $data = array(
            'id' => $this->id->toString(),
            'number' => $this->number->toInteger(),
            'name' => $this->name->toString(),
            'description' => $this->description->toString(),
            'values' => null
        );

        if ($this->values instanceof GeologicalLayerValues) {
            $data['values'] = $this->values->toArray();
        }

        return $data;
    }

    public static function fromArray(array $layer): GeologicalLayer
    {
        $self = new self();
        $self->id = GeologicalLayerId::fromString($layer['id']);
        $self->number = GeologicalLayerNumber::fromInteger($layer['number']);
        $self->name = GeologicalLayerName::fromString($layer['name']);
        $self->description = GeologicalLayerName::fromString($layer['description']);

        if (! is_null($layer['values'])) {
            $self->values = GeologicalLayerValues::fromArray($layer['values']);
        }

        return $self;
    }

    public static function fromParams(GeologicalLayerId $id, GeologicalLayerNumber $number, GeologicalLayerName $name, GeologicalLayerDescription $description, ?GeologicalLayerValues $values = null): GeologicalLayer
    {
        $self = new self();
        $self->id = $id;
        $self->number = $number;
        $self->name = $name;
        $self->description = $description;

        if ($values instanceof GeologicalLayerValues){
            $self->values = $values;
        }

        return $self;
    }

    public function layerNumber(): GeologicalLayerNumber
    {
        return $this->number;
    }

    public function updateValues(GeologicalLayerValues $values): GeologicalLayer
    {
        $self = new self();
        $self->id = $this->id;
        $self->number = $this->number;
        $self->name = $this->name;
        $self->description = $this->description;
        $self->values = $values;
        return $self;
    }
}
