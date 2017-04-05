<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Id\ObservationPointId;

class ObservationPoint
{

    /** @var  ObservationPointId */
    protected $id;

    /** @var  Geometry */
    protected $geometry;

    /** @var  ObservationPointName */
    protected $name;

    /** @var  RiverStages */
    protected $riverStages;

    public static function fromIdNameAndGeometry(ObservationPointId $id, ObservationPointName $name, Geometry $geometry): ObservationPoint
    {
        return new self($id, $name, $geometry);
    }

    private function __construct(ObservationPointId $id, ObservationPointName $name, Geometry $geometry)
    {
        $this->id = $id;
        $this->name = $name;
        $this->geometry = $geometry;
        $this->riverStages = RiverStages::create();
    }

    public function addRiverStage(RiverStage $riverStage): ObservationPoint
    {
        $this->riverStages = $this->riverStages->add($riverStage);
        $self = new self($this->id, $this->name, $this->geometry);
        $self->riverStages = $this->riverStages;
        return $self;
    }

    public function name(): ObservationPointName
    {
        return $this->name;
    }
}
