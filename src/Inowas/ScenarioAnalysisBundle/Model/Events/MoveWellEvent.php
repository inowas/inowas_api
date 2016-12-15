<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Event;
use Ramsey\Uuid\Uuid;

class MoveWellEvent extends Event
{
    public function __construct(Uuid $id, Point $newLocation=null)
    {
        parent::__construct();
        $this->payload = [];
        $this->payload['id'] = $id->toString();
        $this->payload['point'] = $newLocation;
        return $this;
    }

    /**
     * @param ModflowModel $model
     * @return void
     */
    public function applyTo(ModflowModel $model)
    {
        $id = $this->payload['id'];
        /** @var WellBoundary $boundary */
        $boundary = $model->getBoundary(Uuid::fromString($id));

        if ($boundary){
            $newLocation = $this->payload['point'];
            $boundary->setGeometry($newLocation);
        }
    }
}