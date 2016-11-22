<?php

namespace Inowas\Flopy\Model\ValueObject;

class StressPeriodOutputControl implements \JsonSerializable
{

    const PRINT_HEAD = 'PRINT HEAD';
    const PRINT_DRAWDOWN = 'PRINT DRAWDOWN';
    const PRINT_BUDGET = 'PRINT BUDGET';
    const SAVE_HEAD = 'SAVE HEAD';
    const SAVE_DRAWDOWN = 'SAVE DRAWDOWN';
    const SAVE_BUDGET = 'SAVE BUDGET';
    const SAVE_IBOUND = 'SAVE IBOUND';

    /** @var  int */
    private $stressPeriod;

    /** @var  int */
    private $timeStep;

    /** @var  string */
    private $type;

    private final function __construct(){}

    public static function create($sp, $ts, $type){
        $instance = new self();
        $instance->stressPeriod = $sp;
        $instance->timeStep = $ts;
        $instance->type = $type;

        return $instance;
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return array(
            'stressPeriod' => $this->stressPeriod,
            'timeStep' => $this->timeStep,
            'type' => $this->type
        );
    }
}
