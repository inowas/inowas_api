<?php

namespace Inowas\ModflowModel\Model\Exception;

final class GeoProcessingMethodNotFoundException extends \InvalidArgumentException
{
    public static function withMethod(string $method)
    {
        return new self(sprintf('GeoProcessing Method %s cannot be found.', $method));
    }
}
