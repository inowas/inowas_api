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

    public static function fromJson(string $json): PumpingRates
    {
        $values = json_decode($json);
        $pumpingRates = array();
        foreach ($values as $value){
            $pumpingRates[] = PumpingRate::fromArray((array)$value);
        }

        $self = new self();
        $self->values = $pumpingRates;
        return $self;
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
