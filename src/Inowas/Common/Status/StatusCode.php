<?php

declare(strict_types=1);

namespace Inowas\Common\Status;

class StatusCode
{

    /** @var int */
    protected $statusCode;

    public static function fromInt(int $code): StatusCode
    {
        return new self($code);
    }

    private function __construct(int $code)
    {
        $this->statusCode = $code;
    }

    public function toInt(): int
    {
        return $this->statusCode;
    }

    public function ok(): bool
    {
        return $this->statusCode === 200;
    }

    public function error(): bool
    {
        return $this->statusCode > 400;
    }
}
