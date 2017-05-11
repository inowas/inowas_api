<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Model\Command\CopyModflowModel;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CopyModflowModelHandler
{

    /** @var  ModflowModelList */
    private $modelList;


    public function __construct(ModflowModelList $modelList, GeoTools $geoTools)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(CopyModflowModel $command)
    {

        $modflowModel = $this->modelList->get($command->baseModelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->baseModelId());
        }

        /** @var ModflowModelAggregate $modflowModel */
        $newModel = $modflowModel->createCopyWithNewIdAndUserId($command->newModelId(), $command->userId());
        $this->modelList->add($newModel);
    }
}
