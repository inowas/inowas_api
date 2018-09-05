<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Status\StatusCode;

class ModflowReadDataResponse
{
    public const VERSION = '3.2.9';
    protected $data = [];

    /** @var  StatusCode */
    protected $statusCode;

    public static function fromJson(string $json): ModflowReadDataResponse
    {
        $arr = json_decode($json, true);
        $self = new self();
        $self->statusCode = StatusCode::fromInt($arr['status_code']);

        if ($self->statusCode->ok()) {
            if (array_key_exists('timeseries', $arr['request'])) {
                $timeSeries = [];
                $data = $arr['response'];
                foreach ($data as [$key, $value]) {
                    $timeSeries[$key] = $value;
                }

                $self->data = $timeSeries;
                return $self;
            }

            $self->data = $arr['response'];
        }

        return $self;
    }

    public function data(): array
    {
        return $this->data;
    }

    public function statusCode(): StatusCode
    {
        return $this->statusCode;
    }
}
