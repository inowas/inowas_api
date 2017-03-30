<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\IBound;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\Exception\SoilmodelNotFoundException;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelAggregate;
use Inowas\Soilmodel\Model\GeologicalLayer;
use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\Soilmodel\Model\SoilmodelList;

final class CreateModflowModelCalculationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowModelList */
    private $modelCalculationList;

    /** @var  SoilmodelList */
    private $soilmodelList;

    public function __construct(ModflowModelList $modelList, SoilmodelList $soilmodelList, ModflowModelCalculationList $modelCalculationList)
    {
        $this->modelCalculationList = $modelCalculationList;
        $this->modelList = $modelList;
        $this->soilmodelList = $soilmodelList;
    }

    public function __invoke(CreateModflowModelCalculation $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->getModflowModel($command);

        $calculationId = $command->calculationId();
        $calculation = $modflowModel->createCalculation($calculationId, $command->scenarioId());
        $this->modelCalculationList->add($calculation);

        /**
         * @TODO
         * Get the units from the userProfile
         */
        $timeUnit = TimeUnit::fromValue(TimeUnit::DAYS);
        $lengthUnit = LengthUnit::fromValue(LengthUnit::METERS);
        $executableName = FileName::fromString('mf2005');
        $startTime = $command->startDateTime();
        $endTime = $command->endDateTime();

        $calculation->updateModelName($modflowModel->name());
        $calculation->updateExecutableName($executableName);
        $calculation->updateGridParameters($modflowModel->gridSize(), $modflowModel->boundingBox());
        $calculation->updateTimeUnit($timeUnit);
        $calculation->updateLengthUnit($lengthUnit);
        $calculation->updateStartDateTime($startTime);
        $calculation->updateEndDateTime($endTime);

        $soilmodel = $this->getSoilModel($modflowModel->soilmodelId());

        $ibound = IBound::fromActiveCellsAndNumberOfLayers($modflowModel->area()->activeCells(), count($soilmodel->layers()));
        $calculation->updateIBound($ibound);

        $top = [];
        /** @var GeologicalLayer $layer */
        foreach ($soilmodel->layers() as $layer) {
            $top[$layer->layerNumber()->toInteger()] = $layer->values()->hTop()->toArray();
        }
        $calculation->updateStrt(Strt::from3DArray($top));
    }

    private function getModflowModel(CreateModflowModelCalculation $command): ModflowModelAggregate
    {
        /** @var ModflowModelAggregate $baseModel */
        $baseModel = $this->modelList->get($command->modflowModelId());

        if (!$baseModel instanceof ModflowModelAggregate){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        if ($command->scenarioId() instanceof ModflowId) {
            $scenario = $baseModel->findScenario($command->scenarioId());
            if (! $scenario instanceof ModflowModelAggregate){
                throw ModflowModelNotFoundException::withScenarioId($command->scenarioId(), $command->modflowModelId());
            }
            return $scenario;
        }

        return $baseModel;
    }

    private function getSoilModel(SoilmodelId $soilmodelId): SoilmodelAggregate
    {
        $soilModel = $this->soilmodelList->get($soilmodelId);
        if (! $soilModel instanceof SoilmodelAggregate){
            throw SoilmodelNotFoundException::withSoilmodelId($soilmodelId);
        }

        return $soilModel;
    }

}
