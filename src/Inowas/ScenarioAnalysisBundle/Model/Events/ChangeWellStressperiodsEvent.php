<?php

namespace Inowas\ScenarioAnalysisBundle\Model\Events;

use Inowas\ModflowBundle\Model\Boundary\WellBoundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Inowas\ScenarioAnalysisBundle\Model\Event;
use Ramsey\Uuid\Uuid;

class ChangeWellStressperiodsEvent extends Event
{
    public function __construct(Uuid $id, array $stressPeriods)
    {
        parent::__construct();
        $this->payload = [];
        $this->payload['id'] = $id->toString();
        $this->payload['stressPeriods'] = $stressPeriods;
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

        $stressperiods = [];
        foreach ($this->payload['stressPeriods'] as $stressperiod){
            $stressperiods[] = StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime($stressperiod->date_time_begin))
                ->setFlux($stressperiod->flux);
        }

        if ($boundary){
            $boundary->setStressPeriods($stressperiods);
        }
    }
}
