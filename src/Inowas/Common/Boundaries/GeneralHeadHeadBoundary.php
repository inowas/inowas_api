<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Id\BoundaryId;

class GeneralHeadHeadBoundary extends AbstractBoundary
{
    public static function create(BoundaryId $boundaryId): GeneralHeadHeadBoundary
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
        return 'ghb';
    }

    /**
     * @return array
     */
    public function metadata(): array
    {
        return [];
    }

    public function dataToJson(): string
    {
        return json_encode([]);
    }
}
