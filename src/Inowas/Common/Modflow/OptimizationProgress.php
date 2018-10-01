<?php

namespace Inowas\Common\Modflow;

use Inowas\ModflowModel\Model\Exception\InvalidJsonException;

class OptimizationProgress
{
    /** @var array */
    private $progress;

    /**
     * @param string $json
     * @return self
     */
    public static function fromJson(string $json): self
    {
        $decoded = json_decode($json, true);
        if (false === $decoded) {
            throw InvalidJsonException::withoutContent();
        }

        return new self($decoded);
    }

    /**
     * @param array $progress
     * @return self
     */
    public static function fromArray(array $progress): self
    {
        return new self($progress);
    }

    /**
     * @param string|null $progress
     * @return self
     */
    public static function fromDB($progress): self
    {
        if (null === $progress) {
            return new self($progress);
        }

        return self::fromJson($progress);
    }

    /**
     * Optimization constructor.
     * @param $progress
     */
    private function __construct($progress)
    {
        $this->progress = $progress;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->progress;
    }

    /**
     * @return array|null
     */
    public function toArray(): ?array
    {
        return $this->progress;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->progress);
    }

    public function sameAs($progress): bool
    {
        if (!$progress instanceof self) {
            return false;
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return $progress->data() == $this->data();
    }

    public function finished(): bool
    {
        $gaFinished = null;
        if (\array_key_exists(OptimizationMethod::METHOD_GA, $this->progress) && \array_key_exists('final', $this->progress[OptimizationMethod::METHOD_GA])) {
            $gaFinished = $this->progress[OptimizationMethod::METHOD_GA]['final'];
        }

        $simplexFinished = null;
        if (\array_key_exists(OptimizationMethod::METHOD_SIMPLEX, $this->progress) && \array_key_exists('final', $this->progress[OptimizationMethod::METHOD_SIMPLEX])) {
            $simplexFinished = $this->progress[OptimizationMethod::METHOD_SIMPLEX]['final'];
        }

        if ($gaFinished === true && $simplexFinished === null) {
            return true;
        }

        if ($simplexFinished === true) {
            return true;
        }

        return false;
    }
}
