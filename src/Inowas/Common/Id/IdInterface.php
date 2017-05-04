<?php

declare(strict_types=1);

namespace Inowas\Common\Id;

interface IdInterface
{
    public static function generate();
    public static function fromString(string $id);
    public function toString(): string;
    public function sameValueAs(IdInterface $other): bool;
}
