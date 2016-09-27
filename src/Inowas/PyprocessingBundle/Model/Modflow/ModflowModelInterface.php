<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use AppBundle\Entity\BoundaryModelObject;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;

interface ModflowModelInterface
{
    public function addBoundary(BoundaryModelObject $boundary);

    public function changeBoundary(BoundaryModelObject $origin, BoundaryModelObject $newBoundary);

    public function removeBoundary(BoundaryModelObject $boundary);

    public function addCalculationProperties(FlopyCalculationProperties $calculationProperties);

    public function isScenario();
}
