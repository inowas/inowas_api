<?php

namespace Inowas\Modflow\Projection\Calculation;

use Inowas\Modflow\Model\Event\ModflowCalculationResultWasAdded;
use Inowas\Modflow\Model\Event\ModflowCalculationWasCreated;
use Inowas\Modflow\Projection\ProjectionInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CalculationsProjector implements ProjectionInterface
{
    /**
     * @return mixed
     */
    public function reset()
    {
        // TODO: Implement reset() method.
    }

}
