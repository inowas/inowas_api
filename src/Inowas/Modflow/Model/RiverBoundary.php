<?php

namespace Inowas\Modflow\Model;

class RiverBoundary extends AbstractModflowBoundary
{

    protected $stage;

    protected $conductivity;

    protected $riverBed;

    public static function create(BoundaryId $boundaryId): RiverBoundary
    {
        return new self($boundaryId);
    }

    public static function createWithIdNameAndGeometry(
        BoundaryId $boundaryId,
        BoundaryName $name,
        BoundaryGeometry $geometry
    ): RiverBoundary
    {
        $self = new self($boundaryId, $name, $geometry);
        return $self;
    }
}
