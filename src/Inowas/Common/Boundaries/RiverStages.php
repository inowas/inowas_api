<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class RiverStages implements \JsonSerializable
{
    /** @var array */
    private $values;

    public static function create(){
        return new self();
    }

    public static function fromJson(string $json): RiverStages
    {
        $values = json_decode($json);
        $riverStages = array();
        foreach ($values as $value){
            $riverStages[] = RiverStage::fromArray((array)$value);
        }

        $self = new self();
        $self->values = $riverStages;
        return $self;
    }

    private function __construct()
    {
        $this->values = [];
    }

    public function add(RiverStage $riverStages): RiverStages
    {
        $this->values[] = $riverStages;
        $self = new self();
        $self->values = $this->values;
        return $self;
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
