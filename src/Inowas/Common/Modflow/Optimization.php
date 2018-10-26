<?php

namespace Inowas\Common\Modflow;

class Optimization
{
    /** @var OptimizationInput */
    private $input;

    /** @var OptimizationState */
    private $state;

    /** @var OptimizationMethodCollection */
    private $methods;

    public static function createEmpty(): self
    {
        $self = new self();
        $self->input = OptimizationInput::fromArray([]);
        $self->methods = OptimizationMethodCollection::fromArray([]);
        $self->state = OptimizationState::fromInt(0);
        return $self;
    }

    /**
     * @param array $arr
     * @return self
     */
    public static function fromArray(array $arr): self
    {
        $self = new self();
        $self->input = OptimizationInput::fromArray($arr['input']);
        $self->methods = OptimizationMethodCollection::fromArray($arr['methods']);
        $self->state = OptimizationState::fromInt($arr['state']);
        return $self;
    }

    /**
     * @return OptimizationInput
     */
    public function input(): OptimizationInput
    {
        return $this->input;
    }

    /**
     * @return OptimizationMethodCollection
     */
    public function methods(): OptimizationMethodCollection
    {
        return $this->methods;
    }

    /**
     * @return OptimizationState
     */
    public function state(): OptimizationState
    {
        return $this->state;
    }

    public function toArray(): array
    {
        return [
            'input' => $this->input->toArray() !== [] ? $this->input->toArray() : null,
            'methods' => $this->methods->toArray(),
            'state' => $this->state->toInt(),
        ];
    }
}
