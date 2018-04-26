<?php
/**
 * Package ModflowOc
 *
 * The list is used for every stress period and time step after the
 * (IPEROC, ITSOC) tuple until a (IPEROC, ITSOC) tuple is entered with
 * and empty list.
 */
declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;


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
        $this->data[] = $ocStressPeriod->toArray();
        return $this;
    }

    public function toArray(): ?array
    {
        # Since flopy 3.2.9 the default behaviour has changed
        # https://github.com/modflowpy/flopy/releases/tag/3.2.9
        # Now if stressPeriodData is null, then binary head output is saved for the last time step of each stress period.
        if (\is_array($this->data) && \count($this->data) > 0) {
            return $this->data;
        }

        return null;
    }

    public function jsonSerialize()
    {
        return $this->toArray();
    }
}
