<?php

declare(strict_types=1);

namespace Inowas\Common;

class FileName
{
    /** @var  string */
    private $fileName;

    public static function fromString(string $fileName): FileName
    {
        $self = new self($fileName);
        return $self;
    }

    private function __construct(string $fileName)
    {
        $this->fileName = $fileName;
    }

    public function toString(): string
    {
        return $this->fileName;
    }

}
