<?php

namespace Inowas\ModflowModel\Model\Exception;

final class GridSizeOutOfRangeException extends \InvalidArgumentException
{
    public static function withNXValue($nX)
    {
        return new self(sprintf('GridSize-Value nX has to be an integer > 0, Value %s given.', $nX));
    }

    public static function withNYValue($nY)
    {
        return new self(sprintf('GridSize-Value nY has to be an integer > 0, Value %s given.', $nY));
    }
}
