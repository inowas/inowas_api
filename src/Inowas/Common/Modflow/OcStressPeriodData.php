<?php
/**
 * Package ModflowOc
 *
 * The list is used for every stress period and time step after the
 * (IPEROC, ITSOC) tuple until a (IPEROC, ITSOC) tuple is entered with
 * and empty list.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class OcStressPeriodData implements \JsonSerializable
{
    /** @var array */
    private $data = [];

    public static function create(): OcStressPeriodData
    {
        return new self();
    }

    public static function fromArray(array $data): OcStressPeriodData
    {
        $self = new self();
        $self->data = $data;
        return $self;
    }

    private function __construct(){}

    public function addStressPeriod(OcStressPeriod $ocStressPeriod): OcStressPeriodData
    {
        $this->data[] = $ocStressPeriod;
        return $this;
    }


    public function toArray()
    {
        return array(
            'stress_period_data' => $this->data
        );
    }

    public function jsonSerialize()
    {
        $this->toArray();
    }
}
