<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\Grid\ColumnNumber;
use Inowas\Common\Grid\LayerNumber;
use Inowas\Common\Grid\RowNumber;
use Inowas\Common\Modflow\NumberOfTimeSteps;
use Inowas\Common\Modflow\Steady;
use Inowas\Common\Modflow\StressPeriodsLength;
use Inowas\Common\Modflow\TimePeriodsNumber;
use Inowas\Common\Modflow\TimeStepMultiplier;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\SoilmodelAggregate;

class DisPackageAdapter
{
    public function __invoke(
        ModflowCalculationAggregate $calculation,
        ModflowModelAggregate $modflowModel,
        SoilmodelAggregate $soilmodel
    ){
        return DisPackage::fromParams(
            LayerNumber::fromInteger(count($soilmodel->layers())),
            RowNumber::fromInteger($calculation->gridSize()->nY()),
            ColumnNumber::fromInteger($calculation->gridSize()->nX()),
            TimePeriodsNumber::fromInteger($calculation->stressPeriods()->countUniqueTotims()),
            $calculation->delRow(),
            $calculation->delCol(),
            null,
            $soilmodel->topElevation(),
            $soilmodel->bottomElevation(),
            StressPeriodsLength::fromValue($calculation->stressPeriods()->perlen()),
            NumberOfTimeSteps::fromValue($calculation->stressPeriods()->nstp()),
            TimeStepMultiplier::fromValue($calculation->stressPeriods()->tsMult()),
            Steady::fromValue($calculation->stressPeriods()->steady()),
            $calculation->timeUnit(),
            $calculation->lengthUnit(),
            null,
            null,
            $calculation->boundingBox()->upperLeft(),
            null,
            null,
            $calculation->startDateTime()
        );
    }
}
