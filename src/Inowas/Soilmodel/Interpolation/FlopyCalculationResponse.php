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
    protected $budgets;

    /** @var  array */
    protected $drawdowns;

    /** @var  array */
    protected $heads;

    /** @var  int */
    protected $numberOfLayers;

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
            $self->numberOfLayers = $obj->number_of_layers;
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
        $self->numberOfLayers = $arr['number_of_layers'];
        return $self;
    }

    public function toArray(): array
    {
        return array(
            'status_code' => $this->statusCode->toInt(),
            'calculation_id' => $this->calculationId->toString(),
            'budgets' => $this->budgets,
            'drawdowns' => $this->drawdowns,
            'heads' => $this->heads,
            'number_of_layers' => $this->numberOfLayers
        );
    }

    public function calculationId(): ModflowId
    {
        return $this->calculationId;
    }

    public function budgets(): array
    {
        return $this->budgets;
    }

    public function drawdowns(): array
    {
        return $this->drawdowns;
    }

    public function heads(): array
    {
        return $this->heads;
    }

    public function numberOfLayers(): int
    {
        return $this->numberOfLayers;
    }
}
