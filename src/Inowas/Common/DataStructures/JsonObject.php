<?php

namespace Inowas\Common\DataStructures;


use Inowas\ModflowModel\Model\Exception\InvalidJsonException;

class JsonObject implements \JsonSerializable
{
    private $object;

    public static function validateJson(string $json): bool
    {
        if (\json_decode($json, true)) {
            return true;
        }

        return false;
    }

    public static function fromJson(string $json): self
    {
        if (!self::validateJson($json)) {
            throw InvalidJsonException::withoutContent();
        }

        return new self(\json_decode($json));
    }

    private function __construct($content)
    {
        $this->object = $content;
    }

    public function jsonSerialize()
    {
        return $this->object;
    }
}
