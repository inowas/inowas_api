<?php

namespace Inowas\Tool\Model;

final class ToolType
{

    public const GW_MOUNDING = 'T02';
    public const MODEL_SETUP = 'T03';
    public const MCDA = 'T05';
    public const SCENARIOANALYSIS = 'T07';
    public const TRANSPORT_1D = 'T08';
    public const SALTWATER_INTRUSION = 'T09';
    public const TRAVEL_TIME = 'T13';
    public const RIVER_DRAWDOWN = 'T14';

    public static $availableTypes = array(
        'T02',
        'T03',
        'T05',
        'T07',
        'T08',
        'T09',
        'T09A',
        'T09B',
        'T09C',
        'T09D',
        'T09E',
        'T09F',
        'T12',
        'T13',
        'T13A',
        'T13B',
        'T13C',
        'T13D',
        'T13E',
        'T14',
        'T14A',
        'T14B',
        'T14C',
        'T14D',
        'T14E',
        'T18'
    );

    /** @var  string */
    private $type;

    public static function isValid($type): bool
    {
        return \in_array($type, self::$availableTypes, true);
    }

    public static function fromString(string $type): ToolType
    {
        return new self($type);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function sameAs($value): bool
    {
        if (! $value instanceof self){
            return false;
        }

        return $this->type === $value->toString();
    }
}
