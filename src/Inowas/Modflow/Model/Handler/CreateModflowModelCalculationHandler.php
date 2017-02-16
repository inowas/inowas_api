<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
use Inowas\Modflow\Model\Exception\ModflowModelNotFoundException;
use Inowas\Modflow\Model\ModflowCalculationAggregate;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\ModflowModelCalculationList;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelAggregate;

final class CreateModflowModelCalculationHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowModelList */
    private $modelCalculationList;

    /**
     * CreateModflowModelCalculationHandler constructor.
     * @param ModflowModelList $modelList
     * @param ModflowModelCalculationList $modelCalculationList
     */
    public function __construct(ModflowModelList $modelList, ModflowModelCalculationList $modelCalculationList)
    {
        $this->modelList = $modelList;
        $this->modelCalculationList = $modelCalculationList;
    }

    public function __invoke(CreateModflowModelCalculation $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modflowModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modflowModelId());
        }

        $calculationId = $command->calculationId();

        if (is_null($command->scenarioId())){
            $calculation = $modflowModel->createCalculationFromBaseModel($calculationId);
        } else {
            $calculation = $modflowModel->createCalculationFromScenario($calculationId, $command->scenarioId());
        }

        if ($calculation instanceof ModflowCalculationAggregate) {
            $this->modelCalculationList->add($calculation);
        }
    }
}
