<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Event;
use Ramsey\Uuid\Uuid;

class ChangeWellNameEvent extends Event
{
    public function __construct(Uuid $id, string $name = '')
    {
        parent::__construct();
        $this->payload = [];
        $this->payload['id'] = $id->toString();
        $this->payload['name'] = $name;
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
            $name = $this->payload['name'];
            $boundary->setName($name);
        }
    }
}
