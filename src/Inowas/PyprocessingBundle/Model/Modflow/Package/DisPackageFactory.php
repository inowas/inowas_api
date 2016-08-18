<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Entity\SoilModel;
use AppBundle\Model\GridSize;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;

class DisPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){
        $dis = new DisPackage();

        if (! $model->hasSoilModel()) {
            throw new InvalidArgumentException('Model has no soilmodel.');
        }

        $soilModel = $model->getSoilModel();

        if (! $soilModel->hasGeologicalLayers()){
            throw new InvalidArgumentException('Model has no layers.');
        }

        $dis->setNlay($soilModel->getGeologicalLayers()->count());


        if (! $model->getGridSize() instanceof GridSize){
            throw new InvalidArgumentException('Model has no valid GridSize.');
        }

        $gridSize = $model->getGridSize();

        if (! (is_int($gridSize->getNX() && is_int($gridSize->getNY())))){
            throw new InvalidArgumentException('Model has no valid GridSize.');
        }

        $dis->setNcol($model->getGridSize()->getNX());
        $dis->setNrow($model->getGridSize()->getNY());

        if (! (is_int($model->getNumberOfStressPeriods()) && $model->getNumberOfStressPeriods() > 0)){
            throw new InvalidArgumentException('Model no StressPeriods.');
        }

        $dis->setNper($model->getNumberOfStressPeriods());

        // deltaCol
        // deltaRow
        // layerConfiningBed

        /** @var GeologicalLayer $topLayer */
        $topLayer = $soilModel->getLayerByNumber(0);
        $dis->setTop($topLayer->getTopElevation());






    }
}