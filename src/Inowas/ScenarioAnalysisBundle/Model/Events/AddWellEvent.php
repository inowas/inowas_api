<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Event;

class AddWellEvent extends Event
{
    /**
     * AddWellEvent constructor.
     * @param string $name
     */
    public function __construct(string $name)
    {
        parent::__construct();
        $this->payload = [];
        $this->payload['name'] = $name;
        return $this;
    }

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
}