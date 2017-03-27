<?php

declare(strict_types=1);

namespace Inowas\Common\Storage;

class LayerStorage
{

    /** @var  LayerSpecificStorage */
    protected $ss;

    /** @var  LayerSpecificYield */
    protected $sy;

    public static function fromParams(LayerSpecificStorage $ss, LayerSpecificYield $sy): LayerStorage
    {
        $self = new self();
        $self->ss = $ss;
        $self->sy = $sy;

        return $self;
    }

    public function toArray(): array
    {
        return array(
            'ss' => $this->ss->toArray(),
            'sy' => $this->sy->toArray()
        );
    }

    public static function fromArray(array $data): LayerStorage
    {
        $self = new self();
        $self->ss = LayerSpecificStorage::fromArray($data['ss']);
        $self->sy = LayerSpecificYield::fromArray($data['sy']);

        return $self;
    }
}
