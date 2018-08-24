<?php

namespace Inowas\Common\Modflow;

class Optimization
{
    /** @var OptimizationInput */
    private $input;

    /** @var OptimizationState */
    private $state;

    /** @var OptimizationProgress */
    private $progress;

    /** @var OptimizationSolutions */
    private $solutions;

    public static function createEmpty(): self
    {
        $self = new self();
        $self->input = OptimizationInput::fromArray([]);
        $self->progress = OptimizationProgress::fromArray([]);
        $self->solutions = OptimizationSolutions::fromArray([]);
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
        $self->progress = OptimizationProgress::fromArray($arr['progress']);
        $self->solutions = OptimizationSolutions::fromArray($arr['solutions']);
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
     * @return OptimizationProgress
     */
    public function progress(): OptimizationProgress
    {
        return $this->progress;
    }

    /**
     * @return OptimizationSolutions
     */
    public function solutions(): OptimizationSolutions
    {
        return $this->solutions;
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
            'progress' => $this->progress->toArray() !== [] ? $this->progress->toArray() : null,
            'solutions' => $this->solutions->toArray(),
            'state' => $this->state->toInt(),
        ];
    }
}
