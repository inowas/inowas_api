<?php

declare(strict_types=1);

namespace Inowas\Common\FileSystem;

class UploadedFileType
{
    /** @var  string */
    private $type;

    public static function fromString(string $type): UploadedFileType
    {
        return new self($type);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function toString(): string
    {
        return $this->type;
    }
}
