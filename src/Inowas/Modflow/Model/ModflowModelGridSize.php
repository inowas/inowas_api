<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Modflow\Model\Exception\GridSizeOutOfRangeException;

class ModflowModelGridSize implements \JsonSerializable
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

    public static function fromArray(array $gridSizeArray): ModflowModelGridSize
    {
        if (! array_key_exists('n_x', $gridSizeArray)) {
            throw new \Exception();
        }

        if (! array_key_exists('n_y', $gridSizeArray))
        {
            throw new \Exception();
        }

        return new self($gridSizeArray['n_x'], $gridSizeArray['n_y']);
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

    public function toArray()
    {
        return array(
            'n_x' => $this->nX,
            'n_y' => $this->nY,
        );
    }

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
