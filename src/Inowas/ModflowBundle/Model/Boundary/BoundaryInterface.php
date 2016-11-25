<?php

namespace Inowas\ModflowBundle\Model\Boundary;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\Collections\Collection;
use Inowas\ModflowBundle\Model\ActiveCells;

interface BoundaryInterface {
    public function getType(): string;
    public function getStressPeriods(): Collection;
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells);
    public function getObservationPoint(Point $point = null);
    public function getObservationPoints(): Collection;
    public function addObservationPoint(ObservationPoint $observationPoint);
}