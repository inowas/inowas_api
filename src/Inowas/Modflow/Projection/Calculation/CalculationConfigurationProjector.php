<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Boundaries\ConstantHeadBoundary;
use Inowas\Common\Boundaries\GeneralHeadBoundary;
use Inowas\Common\Boundaries\RechargeBoundary;
use Inowas\Common\Boundaries\RiverBoundary;
use Inowas\Common\Boundaries\WellBoundary;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\FileSystem\Modelworkspace;
use Inowas\Common\Id\IdInterface;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Ibound;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\CalculationStressperiodsWereUpdated;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
use Inowas\Modflow\Model\Event\CalculationWasFinished;
use Inowas\Modflow\Model\Event\CalculationWasQueued;
use Inowas\Modflow\Model\Event\CalculationWasStarted;
use Inowas\Modflow\Model\Packages\Packages;
use Inowas\Modflow\Model\Service\ModflowModelManager;
use Inowas\Modflow\Model\Service\ModflowModelManagerInterface;
use Inowas\Modflow\Model\Service\SoilmodelManagerInterface;
use Inowas\Modflow\Projection\Table;
use Inowas\Soilmodel\Model\SoilmodelId;
use Inowas\SoilmodelBundle\Service\SoilmodelManager;

class CalculationConfigurationProjector extends AbstractDoctrineConnectionProjector
{

    /** @var  ModflowModelManager */
    protected $modflowModelManager;

    /** @var  SoilmodelManager */
    protected $soilmodelManager;

    public function __construct(
        Connection $connection,
        ModflowModelManagerInterface $modelManager,
        SoilmodelManagerInterface $soilmodelManager
    ) {

        $this->modflowModelManager = $modelManager;
        $this->soilmodelManager = $soilmodelManager;

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::CALCULATION_CONFIG);
        $table->addColumn('calculation_id', 'string', ['length' => 36]);
        $table->addColumn('modflow_model_id', 'string', ['length' => 36]);
        $table->addColumn('soilmodel_id', 'string', ['length' => 36]);
        $table->addColumn('user_id', 'string', ['length' => 36]);
        $table->addColumn('start', 'string');
        $table->addColumn('time_unit', 'integer');
        $table->addColumn('length_unit', 'integer');
        $table->addColumn('stress_periods', 'string');
        $table->addColumn('configuration', 'text', ['notnull' => false]);
        $table->addColumn('configuration_hash', 'string', ['length' => 36, 'notnull' => false]);
        $table->addColumn('configuration_state', 'integer', ['default' => 0]);
        $table->addColumn('configuration_response', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['calculation_id', 'modflow_model_id']);
    }

    public function onCalculationWasCreated(CalculationWasCreated $event): void
    {
        $packages = $this->calculatePackages(
            $event->calculationId(),
            $event->modflowModelId(),
            $event->soilModelId(),
            $event->start(),
            $event->timeUnit(),
            $event->lengthUnit(),
            $event->stressPeriods()
        );

        $this->connection->insert(Table::CALCULATION_CONFIG, array(
            'calculation_id' => $event->calculationId()->toString(),
            'modflow_model_id' => $event->modflowModelId()->toString(),
            'soilmodel_id' => $event->soilModelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'start' => $event->start()->toAtom(),
            'time_unit' => $event->timeUnit()->toInt(),
            'length_unit' => $event->lengthUnit()->toInt(),
            'stress_periods' => serialize($event->stressPeriods()),
            'configuration' => json_encode($packages),
            'configuration_hash' => md5(json_encode($packages))
        ));
    }

    public function onCalculationStressperiodsWereUpdated(CalculationStressperiodsWereUpdated $event): void
    {

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT * from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $event->calculationId()->toString()]
        );

        $modflowModelId = ModflowId::fromString($result['modflow_model_id']);
        $soilModelId = SoilmodelId::fromString($result['soilmodel_id']);
        $start = DateTime::fromAtom($result['start']);
        $timeUnit = TimeUnit::fromInt($result['time_unit']);
        $lengthUnit = LengthUnit::fromInt($result['length_unit']);

        $packages = $this->calculatePackages(
            $event->calculationId(),
            $modflowModelId,
            $soilModelId,
            $start,
            $timeUnit,
            $lengthUnit,
            $event->stressPeriods()
        );

        $this->connection->update(Table::CALCULATION_CONFIG, array(
            'start' => $start->toAtom(),
            'time_unit' => $timeUnit->toInt(),
            'length_unit' => $lengthUnit->toInt(),
            'stress_periods' => serialize($event->stressPeriods()),
            'configuration' => json_encode($packages),
            'configuration_hash' => md5(json_encode($packages)),
            'configuration_state' => 0,
            'configuration_response' => ""
        ), array(
            'calculation_id' => $event->calculationId()->toString(),
            'modflow_model_id' => $modflowModelId->toString(),
            'soilmodel_id' => $soilModelId->toString(),
            'user_id' => $event->userId()->toString())
        );
    }

    public function onCalculationWasQueued(CalculationWasQueued $event): void
    {
        $this->connection->update(Table::CALCULATION_CONFIG,
            array('configuration_state' => 1),
            array('calculation_id' => $event->calculationId()->toString())
        );
    }

    public function onCalculationWasStarted(CalculationWasStarted $event): void
    {
        $this->connection->update(Table::CALCULATION_CONFIG,
            array('configuration_state' => 2),
            array('calculation_id' => $event->calculationId()->toString())
        );
    }

    public function onCalculationWasFinished(CalculationWasFinished $event): void
    {
        $this->connection->update(Table::CALCULATION_CONFIG,
            array(
                'configuration_state' => 3,
                'configuration_response' => json_encode($event->response()->toArray())
            ),
            array('calculation_id' => $event->calculationId()->toString())
        );
    }

    private function calculatePackages(ModflowId $calculationId, ModflowId $modflowModelId, SoilmodelId $soilmodelId, DateTime $start, TimeUnit $timeUnit, LengthUnit $lengthUnit, StressPeriods $stressPeriods): Packages
    {
        $packages = $this->getDefaultValuesWithId($calculationId);
        $packages->updateStartDateTime($start);
        $packages->updateTimeUnit($timeUnit);
        $packages->updateLengthUnit($lengthUnit);

        /*
         * Add PackageDetails for DisPackage
         * Grid Properties
         */
        $gridSize = $this->modflowModelManager->getGridSize($modflowModelId);
        $boundingBox = $this->modflowModelManager->getBoundingBox($modflowModelId);
        $packages->updateGridParameters($gridSize, $boundingBox);
        $packages->updatePackageParameter('mf', 'modelworkspace', Modelworkspace::fromString($calculationId->toString()));

        /*
         * Add PackageDetails for DisPackage
         * Layers and Elevations
         */
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
        $activeCells = $this->modflowModelManager->getAreaActiveCells($modflowModelId);
        $iBound = Ibound::fromActiveCellsAndNumberOfLayers($activeCells, $this->soilmodelManager->getNlay($soilmodelId)->toInteger());

        $packages->updatePackageParameter('bas', 'ibound', $iBound);
        $strt = Strt::fromTopAndNumberOfLayers($this->soilmodelManager->getTop($soilmodelId), $this->soilmodelManager->getNlay($soilmodelId)->toInteger());
        $packages->updatePackageParameter('bas', 'strt', $strt);

        /*
         * Add PackageDetails for LpfPackage
         */
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

        /*
         * Add PackageDetails for WelPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, WellBoundary::TYPE) > 0) {
            $welStressPeriodData = $this->modflowModelManager->findWelStressPeriodData($modflowModelId, $stressPeriods, $start, $timeUnit);
            $packages->updatePackageParameter('wel', 'StressPeriodData', $welStressPeriodData);
        }

        /*
         * Add PackageDetails for RchPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, RechargeBoundary::TYPE) > 0) {
        }

        /*
         * Add PackageDetails for RivPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, RiverBoundary::TYPE) > 0) {
            $rivStressPeriodData = $this->modflowModelManager->findRivStressPeriodData($modflowModelId, $stressPeriods, $start, $timeUnit);
            $packages->updatePackageParameter('riv', 'StressPeriodData', $rivStressPeriodData);
        }

        /*
         * Add PackageDetails for GhbPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, GeneralHeadBoundary::TYPE) > 0) {
            $ghbStressPeriodData = $this->modflowModelManager->findGhbStressPeriodData($modflowModelId, $stressPeriods, $start, $timeUnit);
            $packages->updatePackageParameter('ghb', 'StressPeriodData', $ghbStressPeriodData);
        }

        /*
         * Add PackageDetails for ChdPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, ConstantHeadBoundary::TYPE) > 0) {
            $chdStressPeriodData = $this->modflowModelManager->findChdStressPeriodData($modflowModelId, $stressPeriods, $start, $timeUnit);
            $packages->updatePackageParameter('chd', 'StressPeriodData', $chdStressPeriodData);
        }

        return $packages;
    }

    private function getDefaultValuesWithId(IdInterface $id): Packages
    {
        return Packages::createFromDefaultsWithId($id);
    }
}
