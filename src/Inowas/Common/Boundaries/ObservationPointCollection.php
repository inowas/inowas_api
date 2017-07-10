<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\KeyHasUseException;
use Inowas\Common\Exception\KeyInvalidException;
use Inowas\Common\Id\ObservationPointId;

class ObservationPointCollection
{
    private $items = [];

    public static function create(): ObservationPointCollection
    {
        return new self();
    }

    public static function fromArray(array $arr): ObservationPointCollection
    {
        $self = new self();

        foreach ($arr as $item) {
            $observationPoint = ObservationPoint::fromArray($item);
            $self->add($observationPoint);
        }

        return $self;
    }

    private function __construct()
    {}

    public function add(ObservationPoint $observationPoint): void
    {
        $key = $observationPoint->id()->toString();

        if (isset($this->items[$key])) {
            throw new KeyHasUseException("Key $key already in use.");
        }

        $this->items[$key] = $observationPoint;
    }

    public function delete(ObservationPointId $id): void
    {
        $key = $id->toString();

        if (isset($this->items[$key])) {
            unset($this->items[$key]);
            return;
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    public function get(ObservationPointId $id): ObservationPoint
    {
        $key = $id->toString();
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    public function has(ObservationPointId $id): bool
    {
        $key = $id->toString();
        return isset($this->items[$key]);
    }

    public function toArray(): array
    {
        return $this->items;
    }

    public function toArrayValues(): array
    {
        return array_values($this->items);
    }

    public function count(): int
    {
        return count($this->items);
    }
}
