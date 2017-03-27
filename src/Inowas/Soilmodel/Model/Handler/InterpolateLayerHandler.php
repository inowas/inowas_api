<?php

declare(strict_types=1);

namespace Inowas\Soilmodel\Model\Handler;

use Inowas\Common\Conductivity\LayerConductivity;
use Inowas\Common\Conductivity\LayerKX;
use Inowas\Common\Conductivity\LayerKY;
use Inowas\Common\Conductivity\LayerKZ;
use Inowas\Common\Length\LayerHBottom;
use Inowas\Common\Length\LayerHTop;
use Inowas\Common\Storage\LayerSpecificStorage;
use Inowas\Common\Storage\LayerSpecificYield;
use Inowas\Common\Storage\LayerStorage;
use Inowas\Soilmodel\Interpolation\InterpolationConfiguration;
use Inowas\Soilmodel\Interpolation\PyModellingInterpolation;
use Inowas\Soilmodel\Model\BoreLogAggregate;
use Inowas\Soilmodel\Model\BoreLogId;
use Inowas\Soilmodel\Model\BoreLogList;
use Inowas\Soilmodel\Model\Command\InterpolateLayer;
use Inowas\Soilmodel\Model\Exception\LayerNotFoundException;
use Inowas\Soilmodel\Model\Exception\SoilmodelNotFoundException;
use Inowas\Soilmodel\Model\Exception\WriteAccessFailedException;
use Inowas\Soilmodel\Model\GeologicalLayer;
use Inowas\Soilmodel\Model\GeologicalLayerNumber;
use Inowas\Soilmodel\Model\GeologicalLayerValues;
use Inowas\Soilmodel\Model\Horizon;
use Inowas\Soilmodel\Model\SoilmodelAggregate;
use Inowas\Soilmodel\Model\SoilmodelList;
use Inowas\SoilmodelBundle\Model\PointValue;

final class InterpolateLayerHandler
{

    /** @var  SoilmodelList */
    private $soilmodelList;

    /** @var  BoreLogList */
    private $boreLogList;

    /** @var  PyModellingInterpolation */
    private $interpolation;

    public function __construct(SoilmodelList $soilmodelList, BoreLogList $boreLogList, PyModellingInterpolation $interpolation)
    {
        $this->boreLogList = $boreLogList;
        $this->interpolation = $interpolation;
        $this->soilmodelList = $soilmodelList;
    }

    public function __invoke(InterpolateLayer $command)
    {
        $soilmodel = $this->soilmodelList->get($command->soilmodelId());

        if (! $soilmodel instanceof SoilmodelAggregate){
            throw SoilmodelNotFoundException::withSoilModelId($command->soilmodelId());
        }

        if (! $soilmodel->userHasWriteAccess($command->userId())){
            throw WriteAccessFailedException::withSoilModelAndUserId($command->soilmodelId(), $command->userId());
        }

        $layer = null;
        /** @var GeologicalLayer $l */
        foreach ($soilmodel->layers() as $l){
            if ($l->layerNumber()->toInteger() == $command->layerNumber()->toInteger()){
                $layer = $l;
            }
        }

        if (! $layer instanceof GeologicalLayer){
            throw LayerNotFoundException::withLayerNumber($command->layerNumber());
        }

        $baseConfiguration = new InterpolationConfiguration();
        $baseConfiguration->addMethod(InterpolationConfiguration::METHOD_GAUSSIAN);
        $baseConfiguration->addMethod(InterpolationConfiguration::METHOD_MEAN);
        $baseConfiguration->setBoundingBox($command->boundingBox());
        $baseConfiguration->setGridSize($command->gridSize());

        $boreLogs = [];
        foreach ($soilmodel->boreLogs() as $boreLogId => $value){
            $boreLogs[] = $this->boreLogList->get(BoreLogId::fromString($boreLogId));
        }

        $layerValues = GeologicalLayerValues::fromParams(
            $this->interpolateHTop($baseConfiguration, $boreLogs, $layer->layerNumber()),
            $this->interpolateHBot($baseConfiguration, $boreLogs, $layer->layerNumber()),
            $this->interpolateConductivity($baseConfiguration, $boreLogs, $layer->layerNumber()),
            $this->interpolateStorage($baseConfiguration, $boreLogs, $layer->layerNumber())
        );

        $soilmodel->updateGeologicalLayerValues($layer->id(), $layerValues);
    }

    private function interpolateHTop(InterpolationConfiguration $configuration, array $boreLogs, GeologicalLayerNumber $layerNumber): LayerHTop
    {
        $configuration = clone $configuration;
        /** @var BoreLogAggregate $boreLog */
        foreach ($boreLogs as $boreLog) {
            $point = $boreLog->location()->toPoint();
            /** @var Horizon $horizon */
            foreach ($boreLog->horizons() as $horizon){
                if ($horizon->layerNumber()->sameAs($layerNumber)){
                    $configuration->addPointValue(new PointValue($point, $horizon->hTop()->toMeters()));
                }
            }
        }

        $result = $this->interpolation->interpolate($configuration);
        return LayerHTop::fromArray($result->result());
    }

    private function interpolateHBot(InterpolationConfiguration $configuration, array $boreLogs, GeologicalLayerNumber $layerNumber): LayerHBottom
    {
        $configuration = clone $configuration;
        /** @var BoreLogAggregate $boreLog */
        foreach ($boreLogs as $boreLog) {
            $point = $boreLog->location()->toPoint();
            /** @var Horizon $horizon */
            foreach ($boreLog->horizons() as $key => $horizon){
                if ($horizon->layerNumber()->sameAs($layerNumber)){
                    $configuration->addPointValue(new PointValue($point, $horizon->hBot()->toMeters()));
                }
            }
        }

        $result = $this->interpolation->interpolate($configuration);
        return LayerHBottom::fromArray($result->result());
    }

    private function interpolateConductivity(InterpolationConfiguration $configuration, array $boreLogs, GeologicalLayerNumber $layerNumber): LayerConductivity
    {
        $kxConfiguration = clone $configuration;
        $kyConfiguration = clone $configuration;
        $kzConfiguration = clone $configuration;


        /** @var BoreLogAggregate $boreLog */
        foreach ($boreLogs as $boreLog) {
            $point = $boreLog->location()->toPoint();
            /** @var Horizon $horizon */
            foreach ($boreLog->horizons() as $key => $horizon){
                if ($horizon->layerNumber()->sameAs($layerNumber)){
                    $kxConfiguration->addPointValue(new PointValue($point, $horizon->conductivity()->kx()->mPerDay()));
                    $kyConfiguration->addPointValue(new PointValue($point, $horizon->conductivity()->ky()->mPerDay()));
                    $kzConfiguration->addPointValue(new PointValue($point, $horizon->conductivity()->kz()->mPerDay()));
                }
            }
        }

        $resultKx = $this->interpolation->interpolate($kxConfiguration);
        $resultKy = $this->interpolation->interpolate($kyConfiguration);
        $resultKz = $this->interpolation->interpolate($kzConfiguration);

        return LayerConductivity::fromXYZinMPerDay(
            LayerKX::fromArray($resultKx->result()),
            LayerKY::fromArray($resultKy->result()),
            LayerKZ::fromArray($resultKz->result())
        );
    }

    private function interpolateStorage(InterpolationConfiguration $configuration, array $boreLogs, GeologicalLayerNumber $layerNumber): LayerStorage
    {
        $ssConfiguration = clone $configuration;
        $syConfiguration = clone $configuration;

        /** @var BoreLogAggregate $boreLog */
        foreach ($boreLogs as $boreLog) {
            $point = $boreLog->location()->toPoint();
            /** @var Horizon $horizon */
            foreach ($boreLog->horizons() as $key => $horizon){
                if ($horizon->layerNumber()->sameAs($layerNumber)){
                    $ssConfiguration->addPointValue(new PointValue($point, $horizon->storage()->ss()->toFloat()));
                    $syConfiguration->addPointValue(new PointValue($point, $horizon->storage()->sy()->toFloat()));
                }
            }
        }

        $resultSs = $this->interpolation->interpolate($ssConfiguration);
        $resultSy = $this->interpolation->interpolate($syConfiguration);

        return LayerStorage::fromParams(
            LayerSpecificStorage::fromArray($resultSs->result()),
            LayerSpecificYield::fromArray($resultSy->result())
        );
    }
}
