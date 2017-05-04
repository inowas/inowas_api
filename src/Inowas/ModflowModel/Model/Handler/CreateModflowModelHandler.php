<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowModel\Model\Command\CreateModflowModel;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

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

        /**
         * @TODO Retrieve Units from UserProfile
         */

        $lengthUnit = LengthUnit::fromInt(LengthUnit::METERS);
        $timeUnit = TimeUnit::fromInt(TimeUnit::DAYS);

        $modflowModel = ModflowModelAggregate::create(
            $createModflowModel->userId(),
            $createModflowModel->modflowModelId(),
            $lengthUnit,
            $timeUnit
        );

        $this->modelList->add($modflowModel);
    }
}
