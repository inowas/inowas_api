<?php

declare(strict_types=1);

namespace Inowas\Common\FileSystem;

class ModelWorkSpace
{
    /** @var  string */
    private $workSpace;

    public static function fromString(string $workSpace): ModelWorkSpace
    {
        $self = new self($workSpace);
        return $self;
    }

    private function __construct(string $workSpace)
    {
        $this->workSpace = $workSpace;
    }

    public function toString(): string
    {
        return $this->workSpace;
    }
}
