<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Event;
use Ramsey\Uuid\Uuid;

class ChangeWellLayerNumberEvent extends Event
{

    public function __construct(Uuid $id, int $layerNumber = 0)
    {
        parent::__construct();
        $this->payload = [];
        $this->payload['id'] = $id->toString();
        $this->payload['layerNumber'] = $layerNumber;
        return $this;
    }

    /**
     * @param ModflowModel $model
     */
    public function applyTo(ModflowModel $model)
    {
        $id = $this->payload['id'];
        /** @var WellBoundary $boundary */
        $boundary = $model->getBoundary(Uuid::fromString($id));

        if ($boundary){
            $layerNumber = $this->payload['layerNumber'];
            $boundary->setLayerNumber($layerNumber);
        }
    }
}
