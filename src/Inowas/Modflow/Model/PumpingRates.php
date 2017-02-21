<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

class PumpingRates
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
}
