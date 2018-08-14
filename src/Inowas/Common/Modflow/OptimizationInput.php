<?php

namespace Inowas\Common\Modflow;

class OptimizationInput
{
    /** @var array */
    private $data;

    /**
     * @param array $arr
     * @return self
     */
    public static function fromArray(array $arr): self
    {
        return new self($arr);
    }

    /**
     * @param string $json
     * @return self
     */
    public static function fromJSON(string $json): self
    {
        return new self(\json_decode($json, true));
    }

    /**
     * Optimization constructor.
     * @param array $data
     */
    private function __construct(array $data)
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

    public function toArray(): array
    {
        return $this->data;
    }

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
