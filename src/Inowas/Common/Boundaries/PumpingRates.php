<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class PumpingRates implements \JsonSerializable
{
    /** @var array */
    private $values;

    public static function create(){
        return new self();
    }

    private function __construct()
    {
        $this->values = [];
    }

    public function add(PumpingRate $pumpingRate): PumpingRates
    {
        $this->values[] = $pumpingRate;
        return $this;
    }

    public function get(): array
    {
        return $this->values;
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->values;
    }
}
