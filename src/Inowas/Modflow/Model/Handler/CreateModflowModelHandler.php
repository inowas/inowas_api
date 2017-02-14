<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModel;

final class CreateModflowModelHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /**
     * CreateModflowModelHandler constructor.
     * @param ModflowModelList $modelList
     */
    public function __construct(ModflowModelList $modelList)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(CreateModflowModel $createModflowModel)
    {
        $modflowModel = ModflowModel::create(
            $createModflowModel->userId(),
            $createModflowModel->modflowModelId()
        );

        $this->modelList->add($modflowModel);
    }
}
