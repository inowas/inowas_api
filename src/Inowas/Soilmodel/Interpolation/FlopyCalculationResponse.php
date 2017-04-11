<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Interpolation;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Status\StatusCode;

class FlopyCalculationResponse
{

    /** @var  StatusCode */
    protected $statusCode;

    /** @var ModflowId */
    protected $calculationId;

    /** @var  array */
    protected $heads;

    /** @var  array */
    protected $drawdowns;

    /** @var  array */
    protected $budgets;

    public static function fromJson(string $json): FlopyCalculationResponse
    {
        $obj = \GuzzleHttp\json_decode($json);
        $self = new self();
        $self->statusCode = StatusCode::fromInt((int)$obj->status_code);
        $self->calculationId = ModflowId::fromString($obj->id);

        if ($self->statusCode->ok()) {
            $self->budgets = $obj->budgets;
            $self->drawdowns = $obj->drawdowns;
            $self->heads = $obj->heads;
        }

        return $self;
    }

    public static function fromArray(array $arr): FlopyCalculationResponse
    {
        $self = new self();
        $self->statusCode = StatusCode::fromInt((int)$arr['status_code']);
        $self->calculationId = ModflowId::fromString($arr['calculation_id']);
        $self->budgets = $arr['budgets'];
        $self->drawdowns = $arr['drawdowns'];
        $self->heads = $arr['heads'];
        return $self;
    }

    public function toArray(): array
    {
        return array(
            'status_code' => $this->statusCode->toInt(),
            'calculation_id' => $this->calculationId->toString(),
            'budgets' => $this->budgets,
            'drawdowns' => $this->drawdowns,
            'heads' => $this->heads
        );
    }

    public function calculationId(): ModflowId
    {
        return $this->calculationId;
    }
}
