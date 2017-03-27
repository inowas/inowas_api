<?php

declare(strict_types=1);

namespace Inowas\Common\FileSystem;

class FileExtension
{

    /** @var  string */
    private $extension;

    public static function fromString(string $extension): FileExtension
    {
        $self = new self($extension);
        return $self;
    }

    private function __construct(string $extension)
    {
        $this->extension = $extension;
    }

    public function toString(): string
    {
        return $this->extension;
    }
}
