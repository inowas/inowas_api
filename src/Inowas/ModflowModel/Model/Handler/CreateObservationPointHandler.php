<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Handler;

use Inowas\Common\Boundaries\DateTimeValueFactory;
use Inowas\Common\Boundaries\ObservationPoint;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\ModflowModel\Model\Command\CreateObservationPoint;
use Inowas\ModflowModel\Model\Exception\ModflowModelNotFoundException;
use Inowas\ModflowModel\Model\Exception\WriteAccessFailedException;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Service\BoundaryManager;
use Inowas\ModflowModel\Service\ModflowModelManager;

final class CreateObservationPointHandler
{

    /** @var BoundaryManager */
    private $boundaryManager;

    /** @var  ModflowModelList */
    private $modelList;

    /** @var  ModflowModelManager */
    private $modelManager;

    /**
     * CreateObservationPointHandler constructor.
     * @param ModflowModelList $modelList
     * @param BoundaryManager $boundaryManager
     * @param ModflowModelManager $modelManager
     */
    public function __construct(ModflowModelList $modelList, BoundaryManager $boundaryManager, ModflowModelManager $modelManager)
    {
        $this->boundaryManager = $boundaryManager;
        $this->modelList = $modelList;
        $this->modelManager = $modelManager;
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
        $boundaryType = $this->boundaryManager->getBoundaryType($command->modelId(), $command->boundaryId());

        $startDate = DateTime::fromDateTimeImmutable(new \DateTimeImmutable('2000-01-01'));
        $stressPeriods = $this->modelManager->getStressPeriodsByModelId($command->modelId());
        if ($stressPeriods instanceof StressPeriods) {
            $startDate = $stressPeriods->start();
        }

        $dateTimeValue = DateTimeValueFactory::create($boundaryType, $startDate);
        $observationPoint->addDateTimeValue($dateTimeValue);
    }
}
