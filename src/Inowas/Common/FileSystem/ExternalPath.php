<?php

declare(strict_types=1);

namespace Inowas\Common\FileSystem;

class ExternalPath
{
    /** @var  string */
    private $externalPath;

    public static function fromValue(?string $externalPath): ExternalPath
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
