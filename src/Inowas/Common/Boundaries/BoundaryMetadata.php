<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class BoundaryMetadata
{
    /**
     * @var array
     */
    private $metadata;

    public static function create(): BoundaryMetadata
    {
        return new self();
    }

    public static function fromArray(array $arr): BoundaryMetadata
    {
        $self = new self();
        $self->metadata = $arr;
        return $self;
    }

    private function __construct()
    {
        $this->metadata = [];
    }

    public function addWellType(WellType $wellType): BoundaryMetadata
    {
        $this->metadata['well_Type'] = $wellType->toString();
        return $this;
    }

    public function toArray(): array
    {
        return $this->metadata;
    }
}
