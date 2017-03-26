<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Handler;

use Inowas\Soilmodel\Model\BoreLogAggregate;
use Inowas\Soilmodel\Model\Command\InterpolateLayerValues;
use Inowas\Soilmodel\Model\Exception\SoilmodelNotFoundException;
use Inowas\Soilmodel\Model\Exception\WriteAccessFailedException;
use Inowas\Soilmodel\Model\Horizon;
use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelList;
use Inowas\SoilmodelBundle\Model\PointValue;
use Inowas\SoilmodelBundle\Service\Interpolation;
use Inowas\SoilmodelBundle\Service\PyModellingInterpolationService;

final class InterpolateLayerValuesHandler
{

    /** @var  SoilmodelList */
    private $soilmodelList;

    /** @var  PyModellingInterpolationService */
    private $interpolation;

    public function __construct(SoilmodelList $soilmodelList, PyModellingInterpolationService $interpolation)
    {
        $this->interpolation = $interpolation;
        $this->soilmodelList = $soilmodelList;
    }

    public function __invoke(InterpolateLayerValues $command)
    {
        $soilmodel = $this->soilmodelList->get($command->soilmodelId());

        if (! $soilmodel instanceof SoilmodelAggregate){
            throw SoilmodelNotFoundException::withSoilModelId($command->soilmodelId());
        }

        if (! $soilmodel->userHasWriteAccess($command->userId())){
            throw WriteAccessFailedException::withSoilModelAndUserId($command->soilmodelId(), $command->userId());
        }

        // Top-Values
        $boreLogs = $soilmodel->boreLogs();

        $config = new Interpolation();
        $config->addMethod(Interpolation::METHOD_GAUSSIAN);
        $config->setBoundingBox($command->boundingBox());
        $config->setGridSize($command->gridSize());

        /** @var BoreLogAggregate $boreLog */
        foreach ($boreLogs as $boreLog) {
            $point = $boreLog->location()->toPoint();
            /** @var Horizon $horizon */
            foreach ($boreLog->horizons() as $key => $horizon){
                if ($key = $command->layerNumber()->toInteger()){
                    $config->addPointValue(new PointValue($point, $horizon->hTop()->toMeters()));
                }
            }
        }

        $result = $this->interpolation->interpolate($config);


        $soilmodel->changeName($command->userId(), $command->name());
    }
}
