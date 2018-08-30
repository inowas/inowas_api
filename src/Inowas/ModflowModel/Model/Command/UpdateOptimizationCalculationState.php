<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Command;

use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationState;
use Inowas\ModflowModel\Model\AMQP\ModflowOptimizationResponse;
use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

class UpdateOptimizationCalculationState extends Command implements PayloadConstructable
{

    use PayloadTrait;

    public static function startOptimizationCalculationProcess(ModflowId $modelId, ModflowId $optimizationId): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::started()->toInt()
        ];

        return new self($payload);
    }

    public static function isPreprocessing(ModflowId $optimizationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::preprocessing()->toInt(),
        ];

        return new self($payload);
    }

    public static function preprocessingFinished(ModflowId $optimizationId, CalculationId $calculationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => OptimizationState::preprocessingFinished()->toInt(),
        ];

        return new self($payload);
    }

    public static function queued(ModflowId $optimizationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::queued()->toInt(),
        ];

        return new self($payload);
    }

    public static function calculating(ModflowId $optimizationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::calculating()->toInt(),
        ];

        return new self($payload);
    }

    public static function calculatingWithProgressUpdate(ModflowId $modelId, ModflowOptimizationResponse $response): self
    {
        $payload = [
            'model_id' => $modelId->toString(),
            'optimization_id' => $response->optimizationId()->toString(),
            'response' => $response->toArray(),
            'state' => OptimizationState::calculating()->toInt(),
        ];

        if ($response->progress()->finished()) {
            $payload['state'] = OptimizationState::finished()->toInt();
        }

        if ($response->errored()) {
            $payload['state'] = OptimizationState::errorOptimizationCore()->toInt();
        }

        return new self($payload);
    }

    public static function cancelled(ModflowId $optimizationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::cancelled()->toInt(),
        ];

        return new self($payload);
    }

    public static function errorPublishing(ModflowId $optimizationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::errorPublishing()->toInt(),
        ];

        return new self($payload);
    }

    public static function errorRecalculatingModel(ModflowId $optimizationId): self
    {
        $payload = [
            'optimization_id' => $optimizationId->toString(),
            'state' => OptimizationState::errorRecalculatingModel()->toInt(),
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

    public function optimizationId(): ?ModflowId
    {
        if (!array_key_exists('optimization_id', $this->payload)) {
            return null;
        }

        return ModflowId::fromString($this->payload['optimization_id']);
    }

    public function response(): ?ModflowOptimizationResponse
    {
        if (!array_key_exists('optimization_id', $this->payload)) {
            return null;
        }

        return ModflowOptimizationResponse::fromArray($this->payload['response']);
    }

    public function state(): OptimizationState
    {
        return OptimizationState::fromInt($this->payload['state']);
    }
}
