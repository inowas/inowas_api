<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\CreateModflowScenario;
use Inowas\Modflow\Model\ModflowModel;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowScenario;
use Inowas\Modflow\Model\ModflowScenarioList;

final class CreateModflowScenarioHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * CreateModflowScenarioHandler constructor.
     * @param ModflowModelList $modelList
     */
    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(CreateModflowScenario $createModflowScenario)
    {
        /** @var ModflowModel $modflowModel */
        $modflowModel = $this->modelList->get($createModflowScenario->modflowModelId());
        $modflowModel->createScenario($createModflowScenario->userId(), $createModflowScenario->scenarioId());
    }
}
