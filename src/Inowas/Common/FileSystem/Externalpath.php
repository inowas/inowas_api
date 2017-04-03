<?php

declare(strict_types=1);

namespace Inowas\Common\FileSystem;

class Externalpath
{
    /** @var  string */
    private $externalPath;

    public static function fromValue(?string $externalPath): Externalpath
    {
        return new self($externalPath);
    }

    private function __construct(?string $externalPath = null)
    {
        $this->externalPath = $externalPath;
    }

    public function toValue(): ?string
    {
        return $this->externalPath;
    }
}
