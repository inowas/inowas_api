<?php

namespace Inowas\Modflow\Model;

class WellType
{
    const TYPE_PRIVATE_WELL = "prw";
    const TYPE_PUBLIC_WELL = "puw";
    const TYPE_OBSERVATION_WELL = "ow";
    const TYPE_INDUSTRIAL_WELL = "iw";
    const TYPE_SCENARIO_MOVED_WELL = "smw";
    const TYPE_SCENARIO_NEW_INDUSTRIAL_WELL = "sniw";
    const TYPE_SCENARIO_NEW_INFILTRATION_WELL = "snifw";
    const TYPE_SCENARIO_NEW_PUBLIC_WELL = "snpw";
    const TYPE_SCENARIO_NEW_WELL = "snw";
    const TYPE_SCENARIO_REMOVED_WELL = "srw";

    private $type;

    public static function fromString(string $type){
        return new self($type);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function type(){
        return $this->type;
    }
}
