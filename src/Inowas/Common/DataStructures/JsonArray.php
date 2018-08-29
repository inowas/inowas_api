<?php

namespace Inowas\Common\DataStructures;


use Inowas\ModflowModel\Model\Exception\InvalidJsonException;

class JsonArray implements \JsonSerializable
{

    private $arrayContent;

    public static function validateJson(string $json): bool
    {
        if (\json_decode($json, true)) {
            return true;
        }

        return false;
    }

    public static function fromJson(string $json): JsonArray
    {
        if (!self::validateJson($json)) {
            throw InvalidJsonException::withoutContent();
        }

        return new self(\json_decode($json, true));
    }

    private function __construct(array $arrayContent)
    {
        $this->arrayContent = $arrayContent;
    }

    public function jsonSerialize()
    {
        return $this->arrayContent;
    }
}
