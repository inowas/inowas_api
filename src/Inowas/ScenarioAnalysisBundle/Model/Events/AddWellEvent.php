<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Event;

class AddWellEvent extends Event
{
    /**
     * @param ModflowModel $model
     * @return void
     */
    public function applyTo(ModflowModel $model)
    {
        /** @var WellBoundary $well */
        $well = BoundaryFactory::createWel();
        $well->setName($this->payload['name']);
        $model->addBoundary($well);
    }

    public static function fromName(string $name){
        $instance = new self();
        $instance->payload = [];
        $instance->payload['name'] = $name;
        return $instance;
    }
}