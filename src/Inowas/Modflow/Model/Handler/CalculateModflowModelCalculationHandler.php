<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\CalculateModflowModelCalculation;
use Inowas\Modflow\Projection\Calculation\CalculationConfigurationFinder;
use Inowas\Soilmodel\Interpolation\FlopyCalculation;

final class CalculateModflowModelCalculationHandler
{

    /** @var  CalculationConfigurationFinder */
    private $calculationFinder;

    /** @var  FlopyCalculation */
    private $flopyCalculation;

    public function __construct(CalculationConfigurationFinder $calculationFinder, FlopyCalculation $flopyCalculation)
    {
        $this->calculationFinder = $calculationFinder;
        $this->flopyCalculation = $flopyCalculation;
    }

    public function __invoke(CalculateModflowModelCalculation $command)
    {
        $calculation = $this->calculationFinder->getFlopyCalculation($command->calculationId());

        if ($calculation) {
            $this->flopyCalculation->calculate($calculation);
        }
    }
}
