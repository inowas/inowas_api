<?php

namespace Inowas\Common\Modflow;


class OptimizationMethod
{
    public const METHOD_GA = 'GA';
    public const METHOD_SIMPLEX = 'Simplex';

    /** @var string */
    private $name;

    /** @var OptimizationProgress */
    private $progress;

    /** @var OptimizationSolutions */
    private $solutions;

    /**
     * @param OptimizationProgress $progress
     * @param OptimizationSolutions $solutions
     * @return OptimizationMethod
     */
    public static function createSimplex(OptimizationProgress $progress, OptimizationSolutions $solutions): OptimizationMethod
    {
        return new self(self::METHOD_SIMPLEX, $progress, $solutions);
    }

    /**
     * @param OptimizationProgress $progress
     * @param OptimizationSolutions $solutions
     * @return OptimizationMethod
     */
    public static function createGA(OptimizationProgress $progress, OptimizationSolutions $solutions): OptimizationMethod
    {
        return new self(self::METHOD_GA, $progress, $solutions);
    }

    /**
     * @param array $arr
     * @return OptimizationMethod
     */
    public static function fromArray(array $arr): OptimizationMethod
    {
        $name = $arr['name'];
        $progress = OptimizationProgress::fromArray($arr['progress']);
        $solutions = OptimizationSolutions::fromArray($arr['solutions']);
        return new self($name, $progress, $solutions);
    }

    /**
     * OptimizationMethod constructor.
     * @param string $name
     * @param OptimizationProgress $progress
     * @param OptimizationSolutions $solutions
     */
    private function __construct(string $name, OptimizationProgress $progress, OptimizationSolutions $solutions)
    {
        $this->name = $name;
        $this->progress = $progress;
        $this->solutions = $solutions;
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
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'progress' => $this->progress->toArray(),
            'solutions' => $this->solutions->toArray()
        ];
    }

    public function finished(): bool
    {
        return $this->progress->finished();
    }
}
