<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Boundaries\BoundaryType;
use Inowas\Common\Boundaries\ConstantHeadDateTimeValue;
use Inowas\Common\Boundaries\DateTimeValue;
use Inowas\Common\Boundaries\GeneralHeadDateTimeValue;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\Boundaries\RechargeDateTimeValue;
use Inowas\Common\Boundaries\RiverDateTimeValue;
use Inowas\Common\Boundaries\WellDateTimeValue;
use Inowas\GeoTools\Service\GeoTools;
use Inowas\ModflowModel\Infrastructure\Projection\BoundaryList\BoundaryFinder;
use Inowas\ModflowModel\Model\Command\CreateObservationPoint;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;

final class CreateObservationPointHandler
{

    /** @var  ModflowModelList */
    private $modelList;

    /** @var BoundaryFinder */
    private $boundaryFinder;

    /**
     * CreateObservationPointHandler constructor.
     * @param ModflowModelList $modelList
     * @param BoundaryFinder $boundaryFinder
     * @param GeoTools $geoTools
     */
    public function __construct(ModflowModelList $modelList, BoundaryFinder $boundaryFinder, GeoTools $geoTools)
    {
        $this->modelList = $modelList;
    }

    public function __invoke(CreateObservationPoint $command)
    {
        /** @var ModflowModelAggregate $modflowModel */
        $modflowModel = $this->modelList->get($command->modelId());

        if (!$modflowModel){
            throw ModflowModelNotFoundException::withModelId($command->modelId());
        }

        if (! $modflowModel->userId()->sameValueAs($command->userId())){
            throw WriteAccessFailedException::withUserAndOwner($command->userId(), $modflowModel->userId());
        }

        $observationPoint = ObservationPoint::fromIdNameAndGeometry($command->observationPointId(), $command->observationPointName(), $command->geometry());
        $boundaryType = $this->boundaryFinder->getBoundaryType($command->modelId(), $command->boundaryId());

        $startDate = new \DateTimeImmutable('2000-01-01');
        $dateTimeValue = null;
        switch ($boundaryType->toString()) {
            case (BoundaryType::CONSTANT_HEAD):
                $dateTimeValue = ConstantHeadDateTimeValue::fromParams($startDate, 0,0);
                break;
            case (BoundaryType::GENERAL_HEAD):
                $dateTimeValue = GeneralHeadDateTimeValue::fromParams($startDate, 0,0);
                break;
            case (BoundaryType::RECHARGE):
                $dateTimeValue = RechargeDateTimeValue::fromParams($startDate, 0);
                break;
            case (BoundaryType::RIVER):
                $dateTimeValue = RiverDateTimeValue::fromParams($startDate, 0,0, 0);
                break;
            case (BoundaryType::WELL):
                $dateTimeValue = WellDateTimeValue::fromParams($startDate, 0);
                break;
        }

        if (! $dateTimeValue instanceof DateTimeValue) {
            // Todo
            throw new \Exception();
        }

        $observationPoint->addDateTimeValue($dateTimeValue);
        $modflowModel->updateBoundaryActiveCells($command->userId(), $command->boundaryId(), $command->activeCells());
    }
}
