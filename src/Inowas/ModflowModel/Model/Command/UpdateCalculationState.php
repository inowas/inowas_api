<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowModel\Model\AMQP\ModflowCalculationResponse;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateCalculationState extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function startCalculationProcess(ModflowId $modelId): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'state' => CalculationState::calculationProcessStarted()->toInt(),
        ];

        return new self($payload);
    }

    public static function isPreprocessing(ModflowId $modelId): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'state' => CalculationState::preprocessing()->toInt(),
        ];

        return new self($payload);
    }

    public static function preprocessingFinished(ModflowId $modelId, CalculationId $calculationId): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => CalculationState::preprocessingFinished()->toInt(),
        ];

        return new self($payload);
    }

    public static function queued(ModflowId $modelId, CalculationId $calculationId): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => CalculationState::queued()->toInt(),
        ];

        return new self($payload);
    }

    public static function calculating(ModflowId $modelId, CalculationId $calculationId): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => CalculationState::calculating()->toInt(),
        ];

        return new self($payload);
    }

    public static function calculationFinished(ModflowId $modelId, CalculationId $calculationId, ModflowCalculationResponse $response): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => CalculationState::finished()->toInt(),
            'response' => $response->toArray()
        ];

        return new self($payload);
    }

    public function modelId(): ?ModflowId
    {
        if (!array_key_exists('model_id', $this->payload)) {
            return null;
        }

        return ModflowId::fromString($this->payload['model_id']);
    }

    public function calculationId(): ?CalculationId
    {
        if (!array_key_exists('calculation_id', $this->payload)) {
            return null;
        }

        return CalculationId::fromString($this->payload['calculation_id']);
    }

    public function state(): CalculationState
    {
        return CalculationState::fromInt($this->payload['state']);
    }

    public function response(): ModflowCalculationResponse
    {
        return ModflowCalculationResponse::fromArray($this->payload['response']);
    }
}
