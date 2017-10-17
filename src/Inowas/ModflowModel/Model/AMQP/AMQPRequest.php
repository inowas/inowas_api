<?php

namespace Inowas\ModflowModel\Model\AMQP;

abstract class AMQPRequest implements \JsonSerializable
{
    /** @var  array */
    private $body;

    public static function fromArray(array $arr)
    {
        return new static($arr);
    }

    protected function __construct(array $body)
    {
        $this->body = $body;
    }

    public function toArray(): array
    {
        return $this->body;
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
