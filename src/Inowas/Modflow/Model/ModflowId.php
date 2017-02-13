<?php

namespace Inowas\Modflow\Model;

interface ModflowId
{
    public static function generate();
    public static function fromString(string $id);
    public function toString(): string;
    public function sameValueAs($other): bool;
}
