<?php

namespace Inowas\ModflowModel\Infrastructure\EventListener;

use Inowas\ModflowModel\Model\AMQP\FlopyCalculationRequest;
use Inowas\ModflowModel\Model\Event\CalculationWasStarted;
use Inowas\ModflowModel\Service\AMQPFlopyCalculation;
use Inowas\ModflowModel\Service\AMQPModflowCalculation;
use Inowas\ModflowModel\Service\ModflowPackagesManager;

class CalculationWasStartedListener
{
    /** @var  AMQPModflowCalculation */
    private $calculator;

    /** @var  ModflowPackagesManager */
    private $packagesManager;

    /**
     * CalculationWasStartedListener constructor.
     * @param AMQPFlopyCalculation $calculator
     */
    public function __construct(AMQPFlopyCalculation $calculator, ModflowPackagesManager $packagesManager){
        $this->calculator = $calculator;
        $this->packagesManager = $packagesManager;
    }

    public function onCalculationWasStarted(CalculationWasStarted $event): void
    {
        $packages = $this->packagesManager->getPackages($event->calculationId());
        $request = FlopyCalculationRequest::fromParams($event->modelId(), $event->calculationId(), $packages);
        $this->calculator->calculate($request);
    }
}
