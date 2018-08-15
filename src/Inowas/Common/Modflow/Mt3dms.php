<?php

namespace Inowas\Common\Modflow;

use Inowas\ModflowModel\Model\Exception\InvalidJsonException;

class Mt3dms
{
    /** @var array */
    private $data;

    /**
     * @param string $json
     * @return Mt3dms
     */
    public static function fromJson(string $json): Mt3dms
    {
        $decoded = json_decode($json, true);
        if (false === $decoded) {
            throw InvalidJsonException::withoutContent();
        }

        return new self($decoded);
    }

    /**
     * @param array $data
     * @return Mt3dms
     */
    public static function fromArray(array $data): Mt3dms
    {
        return new self($data);
    }

    /**
     * @param string|null $data
     * @return Mt3dms
     */
    public static function fromDB($data): Mt3dms
    {
        if (null === $data) {
            return new self($data);
        }

        return self::fromJson($data);
    }

    /**
     * Mt3dms constructor.
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

    public function enabled(): bool
    {
        if (\array_key_exists('enabled', $this->data)) {
            return $this->data['enabled'];
        }

        return false;
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

    public function sameAs($mt3dms): bool
    {
        if (!$mt3dms instanceof self) {
            return false;
        }

        /** @noinspection TypeUnsafeComparisonInspection */
        return $mt3dms->data() == $this->data();
    }
}
