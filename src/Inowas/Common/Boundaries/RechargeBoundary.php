<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Id\BoundaryId;

class RechargeBoundary extends AbstractBoundary
{
    public static function create(BoundaryId $boundaryId): RechargeBoundary
    {
        $static = new self($boundaryId);
        $static->boundaryId = $boundaryId;
        return $static;
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return "rch";
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
        return json_encode([]);
    }
}
