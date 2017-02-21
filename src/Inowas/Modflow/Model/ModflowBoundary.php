<?php

namespace Inowas\Modflow\Model;

interface ModflowBoundary
{
    public static function create(BoundaryId $boundaryId);

    public function boundaryId(): BoundaryId;

    public function name(): BoundaryName;

    public function geometry(): BoundaryGeometry;

    public function type(): string;

    public function metadata(): array;

    public function dataToJson(): string;
}
