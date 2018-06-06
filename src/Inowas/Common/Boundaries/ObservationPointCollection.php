<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Exception\KeyInvalidException;
use Inowas\Common\Id\ObservationPointId;

class ObservationPointCollection
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @return ObservationPointCollection
     */
    public static function create(): ObservationPointCollection
    {
        return new self();
    }

    /**
     * @param array $arr
     * @param BoundaryType $type
     * @return ObservationPointCollection
     * @throws \Inowas\Common\Exception\KeyHasUseException
     */
    public static function fromArray(array $arr, BoundaryType $type): ObservationPointCollection
    {
        $self = new self();

        foreach ($arr as $item) {
            $observationPoint = ObservationPoint::fromArray($item, $type);
            $self->add($observationPoint);
        }

        return $self;
    }

    /**
     * ObservationPointCollection constructor.
     */
    private function __construct()
    {}

    /**
     * @param ObservationPoint $observationPoint
     */
    public function add(ObservationPoint $observationPoint): void
    {
        $key = $observationPoint->id()->toString();
        $this->items[$key] = $observationPoint;
    }

    /**
     * @param ObservationPointId $id
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function delete(ObservationPointId $id): void
    {
        $key = $id->toString();
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
            return;
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    /**
     * @param ObservationPointId $id
     * @return ObservationPoint
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function get(ObservationPointId $id): ObservationPoint
    {
        $key = $id->toString();
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    /**
     * @param ObservationPointId $id
     * @return bool
     */
    public function has(ObservationPointId $id): bool
    {
        return array_key_exists($id->toString(), $this->items);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];
        /** @var ObservationPoint $item */
        foreach ($this->items as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArrayValues(): array
    {
        return array_values($this->items);
    }

    /**
     * @return ObservationPoint|null
     */
    public function first(): ?ObservationPoint
    {
        if ($this->count() === 0) {
            return null;
        }

        return $this->toArrayValues()[0];
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @return array
     */
    public function getDateTimes(): array
    {
        $dateTimes = [];

        /** @var ObservationPoint $item */
        foreach ($this->items as $item) {
            $dateTimes = array_merge($dateTimes, $item->getDateTimes());
        }

        return array_unique($dateTimes);
    }
}
