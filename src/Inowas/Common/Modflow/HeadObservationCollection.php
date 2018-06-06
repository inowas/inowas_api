<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class HeadObservationCollection implements \JsonSerializable
{
    private $data;

    public static function create(): HeadObservationCollection
    {
        return new self();
    }

    public static function fromArray(?array $arr): HeadObservationCollection
    {
        $self = new self();

        if (null === $arr) {
            return $self;
        }

        foreach ($arr as $item) {
            $self->add(HeadObservation::fromArray($item));
        }

        return $self;
    }

    private function __construct()
    {
        $this->data = [];
    }

    public function add(HeadObservation $headObservation): void
    {
        $this->data[] = $headObservation;
    }

    public function toArray(): ?array
    {
        if (\count($this->data) === 0) {
            return null;
        }

        return $this->data;
    }

    public function jsonSerialize(): ?array
    {
        return $this->toArray();
    }

    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toArray() === $this->toArray();
    }
}
