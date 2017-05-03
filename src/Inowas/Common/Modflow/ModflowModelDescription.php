<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class ModflowModelDescription
{
    /** @var string */
    private $name;

    public static function fromString(string $name): ModflowModelDescription
    {
        return new self($name);
    }

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }
}
