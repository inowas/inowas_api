<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Handler;

use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Modflow\Model\Command\CreateModflowModel;
use Inowas\Modflow\Model\ModflowModelList;
use Inowas\Modflow\Model\ModflowModelAggregate;

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
