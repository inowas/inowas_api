<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class BoundaryMetadata
{
    /**
     * @var array
     */
    private $metadata;

    public static function fromArray(array $metadata): BoundaryMetadata
    {
        return new self($metadata);
    }

    private function __construct(array $metadata)
    {
        $this->metadata = $metadata;
    }

    public function toArray(): array
    {
        return $this->metadata;
    }
}
