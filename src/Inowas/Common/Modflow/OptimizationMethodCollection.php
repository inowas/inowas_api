<?php

namespace Inowas\Common\Modflow;


class OptimizationMethodCollection
{
    /** @var array */
    private $methods = [];

    public static function create(): OptimizationMethodCollection
    {
        return new self();
    }

    /**
     * @param array $arr
     * @return OptimizationMethodCollection
     */
    public static function fromArray(array $arr): OptimizationMethodCollection
    {
        $self = new self();
        foreach ($arr as $item) {
            $self->addMethod(OptimizationMethod::fromArray($item));
        }
        return $self;
    }

    private function __construct()
    {
    }

    public function addMethod(OptimizationMethod $method): void
    {
        $this->methods[] = $method;
    }

    public function updateMethod(OptimizationMethod $method): void
    {
        /** @var OptimizationMethod $method */
        foreach ($this->methods as $key => $value) {
            if ($value->name() === $method->name()) {
                $this->methods[$key] = $method;
                return;
            }
        }
        $this->methods[] = $method;
    }

    public function updateMethods(OptimizationMethodCollection $methods): void
    {
        foreach ($methods->toArray() as $method) {
            $this->updateMethod(OptimizationMethod::fromArray($method));
        }
    }

    public function toArray(): array
    {
        $arr = [];
        /** @var OptimizationMethod $method */
        foreach ($this->methods as $method) {
            $arr[] = $method->toArray();
        }

        return $arr;
    }

    public function finished(): bool
    {
        if (\count($this->methods) === 0) {
            return false;
        }

        /** @var OptimizationMethod $method */
        foreach ($this->methods as $method) {
            if (!$method->finished()) {
                return false;
            }
        }
        return true;
    }
}
