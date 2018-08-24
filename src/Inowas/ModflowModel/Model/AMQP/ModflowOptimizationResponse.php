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
    protected $optimizationId;

    /** @var string */
    protected $message;

    /** @var  array */
    protected $solutions;

    /** @var  array */
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
        $self->optimizationId = ModflowId::fromString($arr['optimization_id']);
        $self->message = $arr['message'] ?? '';
        $self->solutions = $arr['solutions'] ?? [];
        $self->progress = $arr['progress'] ?? [];

        return $self;
    }

    private function __construct()
    {}

    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode->toInt(),
            'optimization_id' => $this->optimizationId->toString(),
            'message' => $this->message,
            'solutions' => $this->solutions,
            'progress' => $this->progress
        ];
    }

    public function statusCode(): StatusCode
    {
        return $this->statusCode;
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
        return OptimizationSolutions::fromArray($this->solutions);
    }

    public function progress(): OptimizationProgress
    {
        return OptimizationProgress::fromArray($this->progress);
    }
}
