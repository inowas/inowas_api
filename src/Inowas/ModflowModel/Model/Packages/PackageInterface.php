<?php

namespace Inowas\ModflowModel\Model\Packages;

interface PackageInterface extends \JsonSerializable
{
    public static function fromDefaults();
    public static function fromArray(array $arr);
    public function isValid(): bool;
    public function toArray(): array;
    public function type();
}
