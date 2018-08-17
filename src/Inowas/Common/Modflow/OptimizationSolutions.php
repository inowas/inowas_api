<?php

namespace Inowas\Common\Modflow;

use Inowas\ModflowModel\Model\Exception\InvalidJsonException;

class OptimizationSolutions
{
    /** @var array */
    private $data;

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
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self($data);
    }

    /**
     * @param string|null $data
     * @return self
     */
    public static function fromDB($data): self
    {
        if (null === $data) {
            return new self($data);
        }

        return self::fromJson($data);
    }

    /**
     * Optimization constructor.
     * @param $data
     */
    private function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @return array|null
     */
    public function toArray(): ?array
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->data);
    }

    public function sameAs($data): bool
    {
        if (!$data instanceof self) {
            return false;
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return $data->data() == $this->data();
    }
}
