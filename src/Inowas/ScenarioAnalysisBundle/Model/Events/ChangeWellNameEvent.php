<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Event;

class ChangeWellNameEvent extends Event
{
    /**
     * @param ModflowModel $model
     * @return void
     */
    protected function applyTo(ModflowModel $model)
    {

    }
}