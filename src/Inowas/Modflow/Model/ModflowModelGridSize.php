<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Modflow\Model\Exception\GridSizeOutOfRangeException;

class ModflowModelGridSize
{

    private $nX;
    private $nY;

    public static function fromXY(int $nX, int $nY): ModflowModelGridSize
    {
        if ($nX<1) {
            throw GridSizeOutOfRangeException::withNXValue($nX);
        }

        if ($nY<1) {
            throw GridSizeOutOfRangeException::withNYValue($nY);
        }

        return new self($nX, $nY);
    }

    private function __construct(int $nX, int $nY)
    {
        $this->nX = $nX;
        $this->nY = $nY;
    }

    public function nX(): int
    {
        return $this->nX;
    }

    public function nY(): int
    {
        return $this->nY;
    }
}
