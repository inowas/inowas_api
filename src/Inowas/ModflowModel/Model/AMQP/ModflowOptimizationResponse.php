<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\AMQP;

use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\OptimizationMethod;
use Inowas\Common\Modflow\OptimizationMethodCollection;
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

    /** @var  OptimizationMethodCollection */
    protected $methods;

    protected $availableMethods = [OptimizationMethod::METHOD_GA, OptimizationMethod::METHOD_SIMPLEX];

    /**
     * @param string $json
     * @return ModflowOptimizationResponse
     */
    public static function fromJson(string $json): self
    {
        $arr = json_decode($json, true);
        if (!\is_array($arr)) {
            throw ResponseNotValidException::withResponse($json);
        }
        return self::fromArray($arr);
    }

    /**
     * @param array $arr
     * @return ModflowOptimizationResponse
     */
    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->statusCode = StatusCode::fromInt((int)$arr['status_code']);
        $self->optimizationId = ModflowId::fromString($arr['optimization_id']);
        $self->message = $arr['message'] ?? '';
        if ($self->statusCode->toInt() === 200) {
            $self->methods = OptimizationMethodCollection::fromArray($arr['methods']);
        }
        return $self;
    }

    private function __construct()
    {
    }

    public function toArray(): array
    {
        return [
            'status_code' => $this->statusCode->toInt(),
            'optimization_id' => $this->optimizationId->toString(),
            'message' => $this->message,
            'methods' => $this->methods()->toArray(),
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

    public function methods(): OptimizationMethodCollection
    {
        if ($this->methods === null) {
            $this->methods = OptimizationMethodCollection::create();
        }
        return $this->methods;
    }

    public function finished(): bool
    {
        return $this->methods()->finished();
    }

    public function errored(): bool
    {
        return $this->statusCode->error();
    }
}
