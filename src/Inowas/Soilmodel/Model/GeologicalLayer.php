<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model;

class GeologicalLayer
{
    /** @var  GeologicalLayerId */
    protected $id;

    /** @var  GeologicalLayerName */
    protected $name;

    public function id(): GeologicalLayerId
    {
        return $this->id;
    }

    public function toArray(): array
    {
        return array(
            'id' => $this->id->toString(),
            'name' => $this->name->toString()
        );
    }

    public static function fromArray(array $layer): GeologicalLayer
    {
        $self = new self();
        $self->id = GeologicalLayerId::fromString($layer['id']);
        $self->name = GeologicalLayerName::fromString($layer['name']);
        return $self;
    }
}
