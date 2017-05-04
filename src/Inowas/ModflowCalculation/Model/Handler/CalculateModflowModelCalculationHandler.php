<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model\Handler;

use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Infrastructure\Projection\Calculation\CalculationConfigurationFinder;
use Inowas\ModflowCalculation\Model\ModflowCalculation;

final class CalculateModflowModelCalculationHandler
{

    /** @var  CalculationConfigurationFinder */
    private $calculationFinder;

    /** @var  ModflowCalculation */
    private $modflowCalculation;

    public function __construct(CalculationConfigurationFinder $calculationFinder, ModflowCalculation $flopyCalculation)
    {
        $this->calculationFinder = $calculationFinder;
        $this->modflowCalculation = $flopyCalculation;
    }

    public function __invoke(CalculateModflowModelCalculation $command)
    {
        $calculation = $this->calculationFinder->getFlopyCalculation($command->calculationId());

        if ($calculation) {
            $this->modflowCalculation->calculate($calculation);
        }
    }
}
