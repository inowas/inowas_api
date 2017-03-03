<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Id\BoundaryId;

class ConstantHeadBoundary extends AbstractBoundary
{

    public static function create(BoundaryId $boundaryId): ConstantHeadBoundary
    {
        return new self($boundaryId);
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return 'chd';
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
