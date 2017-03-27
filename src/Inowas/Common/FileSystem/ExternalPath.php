<?php

declare(strict_types=1);

namespace Inowas\Common\FileSystem;

class ExternalPath
{
    /** @var  string */
    private $externalPath;

    public static function fromString(string $externalPath): ExternalPath
    {
        $self = new self($externalPath);
        return $self;
    }

    public static function none(){
        $self = new self();
        return $self;
    }

    private function __construct(?string $externalPath = null)
    {
        $this->externalPath = $externalPath;
    }

    public function toString(): ?string
    {
        return $this->externalPath;
    }
}
