<?php

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\ModflowModel\Model\Exception\GeoProcessingMethodNotFoundException;

class GeoProcessingRequest extends AbstractAMQPRequest
{

    public const METHOD_EXTRACT_RASTER_DATA = 'extractRasterData';

    public static $availableMethods = ['extractRasterData'];

    public static function withMethodAndParameters(string $method, array $parameters): GeoProcessingRequest
    {
        if (!\in_array($method, self::$availableMethods, true)) {
            throw GeoProcessingMethodNotFoundException::withMethod($method);
        }

        $body = [];
        $body['type'] = 'geoProcessing';
        $body['data'] = [
            'method' => $method,
            'parameters' => $parameters
        ];

        return new static($body);
    }
}
