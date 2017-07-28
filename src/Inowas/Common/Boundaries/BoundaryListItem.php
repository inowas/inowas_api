<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Modflow\Name;

final class BoundaryListItem
{
    /** @var BoundaryId */
    private $id;

    /** @var Name */
    private $name;

    /** @var Geometry */
    private $geometry;

    /** @var  BoundaryType */
    private $type;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryId $id
     * @param Name $name
     * @param Geometry $geometry
     * @param BoundaryType $type
     * @return BoundaryListItem
     */
    public static function fromParams(
        BoundaryId $id,
        Name $name,
        Geometry $geometry,
        BoundaryType $type
    ): BoundaryListItem
    {
        $self = new self();
        $self->id = $id;
        $self->name = $name;
        $self->geometry = $geometry;
        $self->type = $type;
        return $self;
    }

    private function __construct()
    {}

    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'name' => $this->name->toString(),
            'geometry' => $this->geometry->toArray(),
            'type' => $this->type->toString(),
        ];
    }
}
