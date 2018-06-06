<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Exception\KeyHasUseException;
use Inowas\Common\Exception\KeyInvalidException;

class DateTimeValuesCollection
{
    /**
     * @var array
     */
    private $items = [];

    /**
     * @return DateTimeValuesCollection
     */
    public static function create(): DateTimeValuesCollection
    {
        return new self();
    }

    /**
     * @param BoundaryType $type
     * @param array $arr
     * @return DateTimeValuesCollection
     */
    public static function fromTypeAndArray(BoundaryType $type, array $arr): DateTimeValuesCollection
    {
        $self = new self();

        foreach ($arr as $item) {
            $dateTimeValue = DateTimeValueFactory::createFromArray($type, $item);
            $self->add($dateTimeValue);
        }

        return $self;
    }

    /**
     * DateTimeValuesCollection constructor.
     */
    private function __construct()
    {}

    /**
     * @param DateTimeValue $dateTimeValue
     * @param null $key
     * @throws \Inowas\Common\Exception\KeyHasUseException
     */
    public function add(DateTimeValue $dateTimeValue, $key = null): void
    {
        if (null === $key) {
            $this->items[] = $dateTimeValue;
            return;
        }

        if (isset($this->items[$key])) {
            throw new KeyHasUseException("Key $key already in use.");
        }

        $this->items[$key] = $dateTimeValue;
    }

    /**
     * @param $key
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function delete($key): void
    {
        if (isset($this->items[$key])) {
            unset($this->items[$key]);
            return;
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    /**
     * @param $key
     * @return DateTimeValue
     * @throws \Inowas\Common\Exception\KeyInvalidException
     */
    public function get($key): DateTimeValue
    {
        if (isset($this->items[$key])) {
            return $this->items[$key];
        }

        throw new KeyInvalidException("Invalid key $key.");
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $result = [];

        /** @var DateTimeValue $dateTimeValue */
        foreach ($this->items as $dateTimeValue){
            $result[] = $dateTimeValue->toArray();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function toArrayValues(): array
    {
        $result = [];

        /** @var DateTimeValue $dateTimeValue */
        foreach ($this->items as $dateTimeValue){
            $result[] = $dateTimeValue->toArrayValues();
        }

        return $result;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->items);
    }

    /**
     * @param DateTime $dateTime
     * @return DateTimeValue|null
     */
    public function findValueByDateTime(DateTime $dateTime): ?DateTimeValue
    {
        $values = $this->items;
        usort($values, function ($v1, $v2) {

            /** @var $v1 DateTimeValue */
            $dtV1 = $v1->dateTime();

            /** @var $v2 DateTimeValue */
            $dtV2 = $v2->dateTime();

            return ($dtV1 < $dtV2) ? +1 : -1;
        });

        /** @var DateTimeValue $value */
        foreach ($values as $value) {
            if ($dateTime >= $value->dateTime()){
                return $value;
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getDateTimes(): array
    {
        $result = [];

        /** @var DateTimeValue $item */
        foreach ($this->items as $item) {
            $result[] = $item->dateTime();
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getItems(): array
    {
        return $this->items;
    }


}
