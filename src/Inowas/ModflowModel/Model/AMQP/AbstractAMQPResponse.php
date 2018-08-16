<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Status\StatusCode;

abstract class AbstractAMQPResponse
{
    private $body;

    /** @var  StatusCode */
    private $statusCode;

    public static function fromJson(string $json)
    {
        return new static(json_decode($json, true));
    }

    protected function __construct(array $data)
    {
        $this->statusCode = StatusCode::fromInt($data['status_code']);
        $this->body = $data['body'];
    }

    public function body()
    {
        return $this->body;
    }

    public function statusCode(): StatusCode
    {
        return $this->statusCode;
    }

    public function isValid(): bool
    {
        return $this->statusCode->ok();
    }
}
