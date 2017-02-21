<?php

namespace Inowas\Modflow\Model;

class AreaBoundary extends AbstractModflowBoundary
{
    public static function create(BoundaryId $boundaryId): AreaBoundary
    {
        return new self($boundaryId);
    }

    /**
     * @param BoundaryName $name
     * @return AreaBoundary
     */
    public function setName(BoundaryName $name): AreaBoundary
    {
        return new self($this->boundaryId, $name, $this->geometry);
    }

    public function setGeometry(BoundaryGeometry $geometry): AreaBoundary
    {
        return new self($this->boundaryId, $this->name, $geometry);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'area';
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        return [];
    }

    /**
     * @return string
     */
    public function dataToJson(): string
    {
        return "";
    }
}
