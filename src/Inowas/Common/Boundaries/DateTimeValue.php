<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

abstract class DateTimeValue implements \JsonSerializable
{
    abstract public static function fromArray(array $arr);

    abstract public static function fromArrayValues(array $arr);

    abstract public function toArray(): array;

    abstract public function toArrayValues(): array;

    abstract public function dateTime(): \DateTimeImmutable;

    abstract public function values(): array;

    abstract public function type(): string;
}
