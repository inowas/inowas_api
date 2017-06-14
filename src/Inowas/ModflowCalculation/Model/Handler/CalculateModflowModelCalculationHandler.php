<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Handler;

use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Infrastructure\Projection\Calculation\CalculationConfigurationFinder;
use Inowas\ModflowCalculation\Model\ModflowCalculation;
use Inowas\ModflowCalculation\Model\ModflowCalculationAggregate;
use Inowas\ModflowCalculation\Model\ModflowCalculationList;

final class CalculateModflowModelCalculationHandler
{

    /** @var ModflowCalculationList $calculationList */
    private $calculationList;

    /** @var  CalculationConfigurationFinder */
    private $calculationFinder;

    /** @var  ModflowCalculation */
    private $modflowCalculation;


    public function __construct(ModflowCalculationList $calculationList, CalculationConfigurationFinder $calculationFinder, ModflowCalculation $flopyCalculation)
    {
        $this->calculationList = $calculationList;
        $this->calculationFinder = $calculationFinder;
        $this->modflowCalculation = $flopyCalculation;
    }

    public function __invoke(CalculateModflowModelCalculation $command)
    {
        /** @var ModflowCalculationAggregate $calculation */
        $calculation = $this->calculationList->get($command->calculationId());

        if ($calculation) {
            #$calculation->calculationHasQueued();
            $calculationConfiguration = $this->calculationFinder->getCalculationConfiguration($command->calculationId());
            $this->modflowCalculation->calculate($calculationConfiguration);
            #$calculation->calculationHasStarted();
        }
    }
}
