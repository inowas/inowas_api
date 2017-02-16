<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\Command\CreateModflowModelCalculation;
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

    public function __invoke(CreateModflowModelCalculation $createModflowModelCalculation)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($createModflowModelCalculation->modflowModelId());
        $calculationId = $createModflowModelCalculation->calculationId();
        $calculation = $modflowModel->addCalculation($calculationId);
        $this->modelCalculationList->add($calculation);
    }
}
