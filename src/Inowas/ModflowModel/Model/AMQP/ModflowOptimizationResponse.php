<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationProgress;
use Inowas\Common\Modflow\OptimizationSolutions;
use Inowas\Common\Status\StatusCode;
use Inowas\ModflowModel\Model\Exception\ResponseNotValidException;

class ModflowOptimizationResponse
{
    /** @var  StatusCode */
    protected $statusCode;

    /** @var ModflowId */
    protected $modelId;

    /** @var CalculationId */
    protected $calculationId;

    /** @var ModflowId */
    protected $optimizationId;

    /** @var string */
    protected $message;

    /** @var  OptimizationSolutions */
    protected $solutions;

    /** @var  OptimizationProgress */
    protected $progress;


    public static function fromJson(string $json): self
    {
        $arr = json_decode($json, true);
        if (!\is_array($arr)) {
            throw ResponseNotValidException::withResponse($json);
        }
        return self::fromArray($arr);
    }

    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->statusCode = StatusCode::fromInt((int)$arr['status_code']);
        $self->modelId = ModflowId::fromString($arr['model_id']);
        $self->calculationId = CalculationId::fromString($arr['calculation_id']);
        $self->optimizationId = ModflowId::fromString($arr['optimization_id']);
        $self->message = $arr['message'] ?? '';
        $self->solutions = OptimizationSolutions::fromArray($arr['solutions']);
        $self->progress = OptimizationProgress::fromArray($arr['progress'] ?? []);

        return $self;
    }

    private function __construct()
    {}

    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode->toInt(),
            'model_id' => $this->modelId->toString(),
            'calculation_id' => $this->calculationId->toString(),
            'optimization_id' => $this->optimizationId->toString(),
            'message' => $this->message,
            'solutions' => $this->solutions->toArray(),
            'progress' => $this->progress->toArray()
        ];
    }

    public function statusCode(): StatusCode
    {
        return $this->statusCode;
    }

    public function modelId(): ModflowId
    {
        return $this->modelId;
    }

    public function calculationId(): CalculationId
    {
        return $this->calculationId;
    }

    public function message(): string
    {
        return $this->message;
    }

    public function optimizationId(): ModflowId
    {
        return $this->optimizationId;
    }

    public function solutions(): OptimizationSolutions
    {
        return $this->solutions;
    }

    public function progress(): OptimizationProgress
    {
        return $this->progress;
    }
}
