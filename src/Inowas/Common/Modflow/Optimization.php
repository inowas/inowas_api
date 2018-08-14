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

    /** @var OptimizationResults */
    private $results;

    public static function createEmpty(): self
    {
        $self = new self();
        $self->input = OptimizationInput::fromArray([]);
        $self->state = OptimizationState::fromInt(0);
        $self->progress = OptimizationProgress::fromArray([]);
        $self->results = OptimizationProgress::fromArray([]);
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
        $self->state = OptimizationState::fromInt($arr['state']);
        $self->progress = OptimizationProgress::fromArray($arr['progress']);
        $self->results = OptimizationProgress::fromArray($arr['results']);
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
     * @return OptimizationState
     */
    public function state(): OptimizationState
    {
        return $this->state;
    }

    /**
     * @return OptimizationProgress
     */
    public function progress(): OptimizationProgress
    {
        return $this->progress;
    }

    /**
     * @return OptimizationResults
     */
    public function results(): OptimizationResults
    {
        return $this->results;
    }


    public function toArray(): array
    {
        return [
            'input' => $this->input->toArray(),
            'state' => $this->state->toInt(),
            'progress' => $this->progress->toArray(),
            'result' => $this->results->toArray()
        ];
    }
}
