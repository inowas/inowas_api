<?php

declare(strict_types=1);

namespace Inowas\Common\Storage;

class Storage
{

    /** @var  SpecificStorage */
    protected $ss;

    /** @var  SpecificYield */
    protected $sy;

    public static function fromParams(SpecificStorage $ss, SpecificYield $sy): Storage
    {
        $self = new self();
        $self->ss = $ss;
        $self->sy = $sy;

        return $self;
    }

    public function toArray(): array
    {
        return array(
            'ss' => $this->ss->toFloat(),
            'sy' => $this->sy->toFloat()
        );
    }

    public static function fromArray(array $data): Storage
    {
        $self = new self();
        $self->ss = SpecificStorage::fromFloat($data['ss']);
        $self->sy = SpecificYield::fromFloat($data['sy']);

        return $self;
    }

    public function ss(): SpecificStorage
    {
        return $this->ss;
    }

    public function sy(): SpecificYield
    {
        return $this->sy;
    }
}
