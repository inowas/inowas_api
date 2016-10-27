<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use AppBundle\Entity\BoundaryModelObject;
use Inowas\PyprocessingBundle\Model\Modflow\Package\FlopyCalculationProperties;
use Ramsey\Uuid\Uuid;

interface ModflowModelInterface
{
    /**
     * @return Uuid
     */
    public function getId();

    public function addBoundary(BoundaryModelObject $boundary);

    public function changeBoundary(BoundaryModelObject $origin, BoundaryModelObject $newBoundary);

    public function removeBoundary(BoundaryModelObject $boundary);

    public function addCalculationProperties(FlopyCalculationProperties $calculationProperties);

    public function isScenario();

    public function getHeads();
}
