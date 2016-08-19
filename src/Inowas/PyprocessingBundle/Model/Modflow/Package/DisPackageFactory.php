<?php

namespace Inowas\PyprocessingBundle\Model\Modflow\Package;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\ModFlowModel;
use AppBundle\Model\StressPeriod;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy1DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy2DArray;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\Flopy3DArray;

class DisPackageFactory implements PackageFactoryInterface
{
    public function create(ModFlowModel $model){

        $dis = new DisPackage();

        $dis->setNlay($model->getNumberOfLayers());
        $dis->setNrow($model->getNumberOfRows());
        $dis->setNcol($model->getNumberOfColumns());
        $dis->setNper($model->getNumberOfStressPeriods());
        $dis->setDelr(Flopy1DArray::fromValue($model->getDeltaRow(), $model->getNumberOfRows()));
        $dis->setDelc(Flopy1DArray::fromValue($model->getDeltaCol(), $model->getNumberOfColumns()));
        $dis->setLaycbd(Flopy1DArray::fromValue($model->getLayerConfiningBed(), $model->getNumberOfLayers()));

        /** @var GeologicalLayer $topLayer */
        $sortedLayers = $model->getSortedLayers();
        $topLayer = $sortedLayers[0];
        $dis->setTop(Flopy2DArray::fromValue($topLayer->getTopElevation(), $model->getNumberOfRows(), $model->getNumberOfColumns()));


        $bottomElevations = array();
        for ($i=0; $i<count($sortedLayers); $i++){
            $bottomElevations[] = $sortedLayers[$i]->getBottomElevation();
        }

        $dis->setBotm(Flopy3DArray::fromValue($bottomElevations, $model->getNumberOfLayers(), $model->getNumberOfRows(), $model->getNumberOfColumns()));

        $stressPeriods = $model->getSortedStressPeriods();

        $perlen = array();
        $nstp = array();
        $tsmult = array();
        $steady = array();
        for ($i = 0; $i<count($stressPeriods); $i++){

            /** @var StressPeriod $sp */
            $sp = $stressPeriods[$i];

            $perlen[] = $sp->getLengthInDays();
            $nstp[] = $sp->getNumberOfTimeSteps();
            $tsmult[] = $sp->getTimeStepMultiplier();
            $steady[] = $sp->isSteady();
        }

        $dis->setPerlen(Flopy1DArray::fromValue($perlen, $model->getNumberOfStressPeriods()));
        $dis->setNstp(Flopy1DArray::fromValue($nstp, $model->getNumberOfStressPeriods()));
        $dis->setTsmult(Flopy1DArray::fromValue($tsmult, $model->getNumberOfStressPeriods()));
        $dis->setSteady(Flopy1DArray::fromValue($steady, $model->getNumberOfStressPeriods()));

        /** @var StressPeriod $sp */
        $sp = $stressPeriods[0];
        $dis->setStartDatetime(\DateTimeImmutable::createFromMutable($sp->getDateTimeBegin()));

        return $dis;
    }
}