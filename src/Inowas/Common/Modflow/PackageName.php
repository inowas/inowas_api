<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class PackageName
{

    /** @var string */
    protected $packageName;

    public static function fromString(string $packageName): PackageName
    {
        return new self($packageName);
    }

    private function __construct(string $packageName)
    {
        $this->packageName = $packageName;
    }

    public function toString(): string
    {
        return $this->packageName;
    }
}
