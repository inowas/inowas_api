<?php

namespace Inowas\Modflow\Model\Packages;

interface PackageInterface extends \JsonSerializable
{
    public static function fromDefaults();
    public static function fromArray(array $arr);
    public function toArray(): array;
    public function type();
}
