<?php

namespace Inowas\ModflowModel\Service;

use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\Id\CalculationId;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Ibound;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\ModflowModel\Model\ModflowModelAggregate;
use Inowas\ModflowModel\Model\ModflowModelList;
use Inowas\ModflowModel\Model\ModflowPackages;
use Inowas\Soilmodel\Service\SoilmodelManager;

class ModflowPackagesManager
{

    /** @var  ModflowPackagesPersister */
    private $modflowPackagePersister;

    /** @var ModflowModelList */
    private $modflowModelList;

    /** @var  ModflowModelManager */
    private $modflowModelManager;

    /** @var  SoilmodelManager */
    private $soilmodelManager;

    public function __construct(ModflowPackagesPersister $packagePersister, ModflowModelList $modelList, ModflowModelManager $modelManager, SoilmodelManager $soilmodelManager)
    {
        $this->modflowPackagePersister = $packagePersister;
        $this->modflowModelList = $modelList;
        $this->modflowModelManager = $modelManager;
        $this->soilmodelManager = $soilmodelManager;
    }

    public function createFromDefaultsAndSave(): CalculationId
    {
        $packages = ModflowPackages::createFromDefaults();
        return $this->savePackages($packages);
    }

    public function getCalculationId(ModflowId $modelId): CalculationId
    {
        /** @var ModflowModelAggregate $model */
        $model = $this->modflowModelList->get($modelId);
        return $model->calculationId();
    }

    public function getPackages(CalculationId $calculationId): ModflowPackages
    {
        return $this->modflowPackagePersister->load($calculationId);
    }

    public function getPackagesByModelId(ModflowId $modelId): ModflowPackages
    {
        /** @var ModflowModelAggregate $model */
        $model = $this->modflowModelList->get($modelId);
        $calculationId = $model->calculationId();
        return $this->modflowPackagePersister->load($calculationId);
    }

    public function recalculateBoundaries(ModflowId $modelId): CalculationId
    {
        $stressPeriods = $this->modflowModelManager->getStressPeriodsByModelId($modelId);
        return $this->recalculateStressperiods($modelId, $stressPeriods);
    }

    public function recalculate(ModflowId $modelId): CalculationId
    {
        /** @var ModflowModelAggregate $model */
        $model = $this->modflowModelList->get($modelId);
        $calculationId = $model->calculationId();
        $packages = $this->modflowPackagePersister->load($calculationId);
        $packages = $this->calculateAllPackages($modelId, $packages);
        return $this->savePackages($packages);
    }

    public function recalculateSoilmodel(ModflowId $modelId, SoilmodelId $soilmodelId): CalculationId
    {

        $packages = $this->getPackagesByModelId($modelId);

        /*
         * Add PackageDetails for DisPackage
         * Layers and Elevations
         */
        $nLay = $this->soilmodelManager->getNlay($soilmodelId);
        $top = $this->soilmodelManager->getTop($soilmodelId);
        $packages->updatePackageParameter('dis', 'nlay', $nLay);
        $packages->updatePackageParameter('dis', 'top', $top);
        $packages->updatePackageParameter('dis', 'botm', $this->soilmodelManager->getBotm($soilmodelId));

        /*
         * Add PackageDetails for BasPackage
         * Ibound, Strt
         */
        $activeCells = $this->modflowModelManager->getAreaActiveCells($modelId);
        $iBound = Ibound::fromActiveCellsAndNumberOfLayers($activeCells, $nLay->toInteger());

        $packages->updatePackageParameter('bas', 'ibound', $iBound);
        $strt = Strt::fromTopAndNumberOfLayers($top, $nLay->toInteger());
        $packages->updatePackageParameter('bas', 'strt', $strt);

        /*
         * Add PackageDetails for LpfPackage if set
         */
        if ($packages->flowPackageName() === 'lpf') {
            $packages->updatePackageParameter('lpf', 'laytyp', $this->soilmodelManager->getLaytyp($soilmodelId));
            $packages->updatePackageParameter('lpf', 'layavg', $this->soilmodelManager->getLayavg($soilmodelId));
            $packages->updatePackageParameter('lpf', 'chani', $this->soilmodelManager->getChani($soilmodelId));
            $packages->updatePackageParameter('lpf', 'layvka', $this->soilmodelManager->getLayvka($soilmodelId));
            $packages->updatePackageParameter('lpf', 'laywet', $this->soilmodelManager->getLaywet($soilmodelId));
            $packages->updatePackageParameter('lpf', 'ipakcb', $this->soilmodelManager->getIpakcb($soilmodelId));
            $packages->updatePackageParameter('lpf', 'hdry', $this->soilmodelManager->getHdry($soilmodelId));
            $packages->updatePackageParameter('lpf', 'wetfct', $this->soilmodelManager->getWetfct($soilmodelId));
            $packages->updatePackageParameter('lpf', 'iwetit', $this->soilmodelManager->getIwetit($soilmodelId));
            $packages->updatePackageParameter('lpf', 'ihdwet', $this->soilmodelManager->getIhdwet($soilmodelId));
            $packages->updatePackageParameter('lpf', 'hk', $this->soilmodelManager->getHk($soilmodelId));
            $packages->updatePackageParameter('lpf', 'hani', $this->soilmodelManager->getHani($soilmodelId));
            $packages->updatePackageParameter('lpf', 'vka', $this->soilmodelManager->getVka($soilmodelId));
            $packages->updatePackageParameter('lpf', 'ss', $this->soilmodelManager->getSs($soilmodelId));
            $packages->updatePackageParameter('lpf', 'sy', $this->soilmodelManager->getSy($soilmodelId));
            $packages->updatePackageParameter('lpf', 'vkcb', $this->soilmodelManager->getVkcb($soilmodelId));
            $packages->updatePackageParameter('lpf', 'wetdry', $this->soilmodelManager->getWetdry($soilmodelId));
            $packages->updatePackageParameter('lpf', 'storagecoefficient', $this->soilmodelManager->getStoragecoefficient($soilmodelId));
            $packages->updatePackageParameter('lpf', 'constantcv', $this->soilmodelManager->getConstantcv($soilmodelId));
            $packages->updatePackageParameter('lpf', 'thickstrt', $this->soilmodelManager->getThickstrt($soilmodelId));
            $packages->updatePackageParameter('lpf', 'nocvcorrection', $this->soilmodelManager->getNocvcorrection($soilmodelId));
            $packages->updatePackageParameter('lpf', 'novfc', $this->soilmodelManager->getNovfc($soilmodelId));
        }

        /*
         * Add PackageDetails for LpfPackage if set
         */
        if ($packages->flowPackageName() === 'upw') {
            $packages->updatePackageParameter('upw', 'laytyp', $this->soilmodelManager->getLaytyp($soilmodelId));
            $packages->updatePackageParameter('upw', 'layavg', $this->soilmodelManager->getLayavg($soilmodelId));
            $packages->updatePackageParameter('upw', 'chani', $this->soilmodelManager->getChani($soilmodelId));
            $packages->updatePackageParameter('upw', 'layvka', $this->soilmodelManager->getLayvka($soilmodelId));
            $packages->updatePackageParameter('upw', 'laywet', $this->soilmodelManager->getLaywet($soilmodelId));
            $packages->updatePackageParameter('upw', 'ipakcb', $this->soilmodelManager->getIpakcb($soilmodelId));
            $packages->updatePackageParameter('upw', 'hdry', $this->soilmodelManager->getHdry($soilmodelId));
            $packages->updatePackageParameter('upw', 'iphdry', $this->soilmodelManager->getIphdry($soilmodelId));
            $packages->updatePackageParameter('upw', 'hk', $this->soilmodelManager->getHk($soilmodelId));
            $packages->updatePackageParameter('upw', 'hani', $this->soilmodelManager->getHani($soilmodelId));
            $packages->updatePackageParameter('upw', 'vka', $this->soilmodelManager->getVka($soilmodelId));
            $packages->updatePackageParameter('upw', 'ss', $this->soilmodelManager->getSs($soilmodelId));
            $packages->updatePackageParameter('upw', 'sy', $this->soilmodelManager->getSy($soilmodelId));
            $packages->updatePackageParameter('upw', 'vkcb', $this->soilmodelManager->getVkcb($soilmodelId));
        }

        return $this->savePackages($packages);
    }

    public function recalculateStressperiods(ModflowId $modelId, StressPeriods $stressPeriods): CalculationId
    {
        $packages = $this->getPackagesByModelId($modelId);
        $packages->updateStartDateTime($stressPeriods->start());
        $packages->updateTimeUnit($stressPeriods->timeUnit());

        $packages->updatePackageParameter('dis', 'perlen', $stressPeriods->perlen());
        $packages->updatePackageParameter('dis', 'nstp', $stressPeriods->nstp());
        $packages->updatePackageParameter('dis', 'tsmult', $stressPeriods->tsmult());
        $packages->updatePackageParameter('dis', 'steady', $stressPeriods->steady());
        $packages->updatePackageParameter('dis', 'nper', $stressPeriods->nper());

        /*
         * Add PackageDetails for WelPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, WellBoundary::TYPE) > 0) {
            $welStressPeriodData = $this->modflowModelManager->generateWelStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('wel', 'StressPeriodData', $welStressPeriodData);
        }

        /*
         * Add PackageDetails for RchPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, RechargeBoundary::TYPE) > 0) {
            $rchStressPeriodData = $this->modflowModelManager->generateRchStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('rch', 'StressPeriodData', $rchStressPeriodData);
        }

        /*
         * Add PackageDetails for RivPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, RiverBoundary::TYPE) > 0) {
            $rivStressPeriodData = $this->modflowModelManager->generateRivStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('riv', 'StressPeriodData', $rivStressPeriodData);
        }

        /*
         * Add PackageDetails for GhbPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, GeneralHeadBoundary::TYPE) > 0) {
            $ghbStressPeriodData = $this->modflowModelManager->generateGhbStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('ghb', 'StressPeriodData', $ghbStressPeriodData);
        }

        /*
         * Add PackageDetails for ChdPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, ConstantHeadBoundary::TYPE) > 0) {
            $chdStressPeriodData = $this->modflowModelManager->generateChdStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('chd', 'StressPeriodData', $chdStressPeriodData);
        }

        return $this->savePackages($packages);
    }

    public function savePackages(ModflowPackages $packages): CalculationId
    {
        return $this->modflowPackagePersister->save($packages);
    }

    private function calculateAllPackages(ModflowId $modelId, ModflowPackages $packages): ModflowPackages
    {
        $stressPeriods = $this->modflowModelManager->getStressPeriodsByModelId($modelId);
        $packages->updateStartDateTime($stressPeriods->start());
        $packages->updateTimeUnit($stressPeriods->timeUnit());

        $lengthUnit = $this->modflowModelManager->getLengthUnitByModelId($modelId);
        $packages->updateLengthUnit($lengthUnit);

        /*
         * Add PackageDetails for DisPackage
         * Grid Properties
         */
        $gridSize = $this->modflowModelManager->getGridSize($modelId);
        $boundingBox = $this->modflowModelManager->getBoundingBox($modelId);
        $packages->updateGridParameters($gridSize, $boundingBox);

        /*
         * Add PackageDetails for DisPackage
         * Layers and Elevations
         */
        $soilmodelId = $this->modflowModelManager->getSoilmodelIdByModelId($modelId);
        $packages->updatePackageParameter('dis', 'nlay', $this->soilmodelManager->getNlay($soilmodelId));
        $packages->updatePackageParameter('dis', 'top', $this->soilmodelManager->getTop($soilmodelId));
        $packages->updatePackageParameter('dis', 'botm', $this->soilmodelManager->getBotm($soilmodelId));

        /*
         * Add PackageDetails for DisPackage
         * StressPeriods
         */

        $packages->updatePackageParameter('dis', 'perlen', $stressPeriods->perlen());
        $packages->updatePackageParameter('dis', 'nstp', $stressPeriods->nstp());
        $packages->updatePackageParameter('dis', 'tsmult', $stressPeriods->tsmult());
        $packages->updatePackageParameter('dis', 'steady', $stressPeriods->steady());
        $packages->updatePackageParameter('dis', 'nper', $stressPeriods->nper());


        /*
         * Add PackageDetails for BasPackage
         * Ibound, Strt
         */
        $activeCells = $this->modflowModelManager->getAreaActiveCells($modelId);
        $iBound = Ibound::fromActiveCellsAndNumberOfLayers($activeCells, $this->soilmodelManager->getNlay($soilmodelId)->toInteger());

        $packages->updatePackageParameter('bas', 'ibound', $iBound);
        $strt = Strt::fromTopAndNumberOfLayers($this->soilmodelManager->getTop($soilmodelId), $this->soilmodelManager->getNlay($soilmodelId)->toInteger());
        $packages->updatePackageParameter('bas', 'strt', $strt);

        /*
         * Add PackageDetails for LpfPackage if set
         */
        if ($packages->flowPackageName() === 'lpf') {
            $packages->updatePackageParameter('lpf', 'laytyp', $this->soilmodelManager->getLaytyp($soilmodelId));
            $packages->updatePackageParameter('lpf', 'layavg', $this->soilmodelManager->getLayavg($soilmodelId));
            $packages->updatePackageParameter('lpf', 'chani', $this->soilmodelManager->getChani($soilmodelId));
            $packages->updatePackageParameter('lpf', 'layvka', $this->soilmodelManager->getLayvka($soilmodelId));
            $packages->updatePackageParameter('lpf', 'laywet', $this->soilmodelManager->getLaywet($soilmodelId));
            $packages->updatePackageParameter('lpf', 'ipakcb', $this->soilmodelManager->getIpakcb($soilmodelId));
            $packages->updatePackageParameter('lpf', 'hdry', $this->soilmodelManager->getHdry($soilmodelId));
            $packages->updatePackageParameter('lpf', 'wetfct', $this->soilmodelManager->getWetfct($soilmodelId));
            $packages->updatePackageParameter('lpf', 'iwetit', $this->soilmodelManager->getIwetit($soilmodelId));
            $packages->updatePackageParameter('lpf', 'ihdwet', $this->soilmodelManager->getIhdwet($soilmodelId));
            $packages->updatePackageParameter('lpf', 'hk', $this->soilmodelManager->getHk($soilmodelId));
            $packages->updatePackageParameter('lpf', 'hani', $this->soilmodelManager->getHani($soilmodelId));
            $packages->updatePackageParameter('lpf', 'vka', $this->soilmodelManager->getVka($soilmodelId));
            $packages->updatePackageParameter('lpf', 'ss', $this->soilmodelManager->getSs($soilmodelId));
            $packages->updatePackageParameter('lpf', 'sy', $this->soilmodelManager->getSy($soilmodelId));
            $packages->updatePackageParameter('lpf', 'vkcb', $this->soilmodelManager->getVkcb($soilmodelId));
            $packages->updatePackageParameter('lpf', 'wetdry', $this->soilmodelManager->getWetdry($soilmodelId));
            $packages->updatePackageParameter('lpf', 'storagecoefficient', $this->soilmodelManager->getStoragecoefficient($soilmodelId));
            $packages->updatePackageParameter('lpf', 'constantcv', $this->soilmodelManager->getConstantcv($soilmodelId));
            $packages->updatePackageParameter('lpf', 'thickstrt', $this->soilmodelManager->getThickstrt($soilmodelId));
            $packages->updatePackageParameter('lpf', 'nocvcorrection', $this->soilmodelManager->getNocvcorrection($soilmodelId));
            $packages->updatePackageParameter('lpf', 'novfc', $this->soilmodelManager->getNovfc($soilmodelId));
        }

        /*
         * Add PackageDetails for UpwPackage if set
         */
        if ($packages->flowPackageName() === 'upw') {
            $packages->updatePackageParameter('upw', 'laytyp', $this->soilmodelManager->getLaytyp($soilmodelId));
            $packages->updatePackageParameter('upw', 'layavg', $this->soilmodelManager->getLayavg($soilmodelId));
            $packages->updatePackageParameter('upw', 'chani', $this->soilmodelManager->getChani($soilmodelId));
            $packages->updatePackageParameter('upw', 'layvka', $this->soilmodelManager->getLayvka($soilmodelId));
            $packages->updatePackageParameter('upw', 'laywet', $this->soilmodelManager->getLaywet($soilmodelId));
            $packages->updatePackageParameter('upw', 'ipakcb', $this->soilmodelManager->getIpakcb($soilmodelId));
            $packages->updatePackageParameter('upw', 'hdry', $this->soilmodelManager->getHdry($soilmodelId));
            $packages->updatePackageParameter('upw', 'iphdry', $this->soilmodelManager->getIphdry($soilmodelId));
            $packages->updatePackageParameter('upw', 'hk', $this->soilmodelManager->getHk($soilmodelId));
            $packages->updatePackageParameter('upw', 'hani', $this->soilmodelManager->getHani($soilmodelId));
            $packages->updatePackageParameter('upw', 'vka', $this->soilmodelManager->getVka($soilmodelId));
            $packages->updatePackageParameter('upw', 'ss', $this->soilmodelManager->getSs($soilmodelId));
            $packages->updatePackageParameter('upw', 'sy', $this->soilmodelManager->getSy($soilmodelId));
            $packages->updatePackageParameter('upw', 'vkcb', $this->soilmodelManager->getVkcb($soilmodelId));
        }

        /*
         * Add PackageDetails for WelPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, WellBoundary::TYPE) > 0) {
            $welStressPeriodData = $this->modflowModelManager->generateWelStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('wel', 'StressPeriodData', $welStressPeriodData);
        }

        /*
         * Add PackageDetails for RchPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, RechargeBoundary::TYPE) > 0) {
            $rchStressPeriodData = $this->modflowModelManager->generateRchStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('rch', 'StressPeriodData', $rchStressPeriodData);
        }

        /*
         * Add PackageDetails for RivPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, RiverBoundary::TYPE) > 0) {
            $rivStressPeriodData = $this->modflowModelManager->generateRivStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('riv', 'StressPeriodData', $rivStressPeriodData);
        }

        /*
         * Add PackageDetails for GhbPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, GeneralHeadBoundary::TYPE) > 0) {
            $ghbStressPeriodData = $this->modflowModelManager->generateGhbStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('ghb', 'StressPeriodData', $ghbStressPeriodData);
        }

        /*
         * Add PackageDetails for ChdPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modelId, ConstantHeadBoundary::TYPE) > 0) {
            $chdStressPeriodData = $this->modflowModelManager->generateChdStressPeriodData($modelId, $stressPeriods);
            $packages->updatePackageParameter('chd', 'StressPeriodData', $chdStressPeriodData);
        }

        return $packages;
    }
}
