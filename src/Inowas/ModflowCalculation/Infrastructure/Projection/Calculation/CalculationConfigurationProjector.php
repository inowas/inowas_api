<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Infrastructure\Projection\Calculation;

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
use Inowas\ModflowCalculation\Infrastructure\Projection\Table;
use Inowas\ModflowCalculation\Model\ModflowCalculationConfiguration;
use Inowas\ModflowCalculation\Model\Event\CalculationFlowPackageWasChanged;
use Inowas\ModflowCalculation\Model\Event\CalculationPackageParameterWasUpdated;
use Inowas\ModflowCalculation\Model\Event\CalculationStressperiodsWereUpdated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCreated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasFinished;
use Inowas\ModflowCalculation\Model\Event\CalculationWasQueued;
use Inowas\ModflowCalculation\Model\Event\CalculationWasStarted;
use Inowas\ModflowModel\Service\ModflowModelManager;
use Inowas\ModflowModel\Service\ModflowModelManagerInterface;
use Inowas\Soilmodel\Model\SoilmodelManagerInterface;
use Inowas\Common\Soilmodel\SoilmodelId;
use Inowas\Soilmodel\Service\SoilmodelManager;

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
    )
    {

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
        $table->addColumn('stress_periods', 'text', ['notnull' => false]);
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
            'stress_periods' => $this->serialize($event->stressPeriods()),
            'configuration' => json_encode($packages),
            'configuration_hash' => md5(json_encode($packages))
        ));
    }

    public function onCalculationStressperiodsWereUpdated(CalculationStressperiodsWereUpdated $event): void
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT modflow_model_id, soilmodel_id, start, time_unit, length_unit from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $event->calculationId()->toString()]
        );

        $modflowModelId = ModflowId::fromString($result['modflow_model_id']);
        $soilModelId = SoilmodelId::fromString($result['soilmodel_id']);
        $start = DateTime::fromAtom($result['start']);
        $timeUnit = TimeUnit::fromInt($result['time_unit']);
        $lengthUnit = LengthUnit::fromInt($result['length_unit']);

        $configuration = $this->calculatePackages(
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
            'configuration' => json_encode($configuration),
            'configuration_hash' => md5(json_encode($configuration)),
            'configuration_state' => 0,
            'configuration_response' => ""
        ), array(
                'calculation_id' => $event->calculationId()->toString(),
                'modflow_model_id' => $modflowModelId->toString(),
                'soilmodel_id' => $soilModelId->toString(),
                'user_id' => $event->userId()->toString())
        );
    }

    public function onCalculationPackageParameterWasUpdated(CalculationPackageParameterWasUpdated $event): void
    {
        $configuration = $this->getSavedOrDefaultConfigurationById($event->calculationId());
        $configuration->updatePackageParameter($event->packageName(), $event->parameterName(), $event->parameterData());
        $this->storeConfiguration($event->calculationId(), $configuration);
    }

    public function onCalculationFlowPackageWasChanged(CalculationFlowPackageWasChanged $event): void
    {
        $configuration = $this->getSavedOrDefaultConfigurationById($event->calculationId());
        $configuration->changeFlowPackage($event->packageName());
        $this->storeConfiguration($event->calculationId(), $configuration);

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT modflow_model_id, soilmodel_id, start, stress_periods, time_unit, length_unit from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $event->calculationId()->toString()]
        );

        $modflowModelId = ModflowId::fromString($result['modflow_model_id']);
        $soilModelId = SoilmodelId::fromString($result['soilmodel_id']);
        $stressPeriods = $this->unserialize($result['stress_periods']);
        $start = DateTime::fromAtom($result['start']);
        $timeUnit = TimeUnit::fromInt($result['time_unit']);
        $lengthUnit = LengthUnit::fromInt($result['length_unit']);

        $configuration = $this->calculatePackages(
            $event->calculationId(),
            $modflowModelId,
            $soilModelId,
            $start,
            $timeUnit,
            $lengthUnit,
            $stressPeriods
        );

        $this->connection->update(Table::CALCULATION_CONFIG, array(
            'start' => $start->toAtom(),
            'time_unit' => $timeUnit->toInt(),
            'length_unit' => $lengthUnit->toInt(),
            'stress_periods' => $this->serialize($stressPeriods),
            'configuration' => json_encode($configuration),
            'configuration_hash' => md5(json_encode($configuration)),
            'configuration_state' => 0,
            'configuration_response' => ""
        ), array(
                'calculation_id' => $event->calculationId()->toString(),
                'modflow_model_id' => $modflowModelId->toString(),
                'user_id' => $event->userId()->toString())
        );

        $this->storeConfiguration($event->calculationId(), $configuration);
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

    private function calculatePackages(ModflowId $calculationId, ModflowId $modflowModelId, SoilmodelId $soilmodelId, DateTime $start, TimeUnit $timeUnit, LengthUnit $lengthUnit, StressPeriods $stressPeriods): ModflowCalculationConfiguration
    {
        $configuration = $this->getSavedOrDefaultConfigurationById($calculationId);

        $configuration->updateStartDateTime($start);
        $configuration->updateTimeUnit($timeUnit);
        $configuration->updateLengthUnit($lengthUnit);

        /*
         * Add PackageDetails for DisPackage
         * Grid Properties
         */
        $gridSize = $this->modflowModelManager->getGridSize($modflowModelId);
        $boundingBox = $this->modflowModelManager->getBoundingBox($modflowModelId);
        $configuration->updateGridParameters($gridSize, $boundingBox);
        $configuration->updatePackageParameter('mf', 'modelworkspace', Modelworkspace::fromString($calculationId->toString()));

        /*
         * Add PackageDetails for DisPackage
         * Layers and Elevations
         */
        $configuration->updatePackageParameter('dis', 'nlay', $this->soilmodelManager->getNlay($soilmodelId));
        $configuration->updatePackageParameter('dis', 'top', $this->soilmodelManager->getTop($soilmodelId));
        $configuration->updatePackageParameter('dis', 'botm', $this->soilmodelManager->getBotm($soilmodelId));

        /*
         * Add PackageDetails for DisPackage
         * StressPeriods
         */
        $configuration->updatePackageParameter('dis', 'perlen', $stressPeriods->perlen());
        $configuration->updatePackageParameter('dis', 'nstp', $stressPeriods->nstp());
        $configuration->updatePackageParameter('dis', 'tsmult', $stressPeriods->tsmult());
        $configuration->updatePackageParameter('dis', 'steady', $stressPeriods->steady());
        $configuration->updatePackageParameter('dis', 'nper', $stressPeriods->nper());


        /*
         * Add PackageDetails for BasPackage
         * Ibound, Strt
         */
        $activeCells = $this->modflowModelManager->getAreaActiveCells($modflowModelId);
        $iBound = Ibound::fromActiveCellsAndNumberOfLayers($activeCells, $this->soilmodelManager->getNlay($soilmodelId)->toInteger());

        $configuration->updatePackageParameter('bas', 'ibound', $iBound);
        $strt = Strt::fromTopAndNumberOfLayers($this->soilmodelManager->getTop($soilmodelId), $this->soilmodelManager->getNlay($soilmodelId)->toInteger());
        $configuration->updatePackageParameter('bas', 'strt', $strt);

        /*
         * Add PackageDetails for LpfPackage if set
         */
        if ($configuration->flowPackageName() == 'lpf') {
            $configuration->updatePackageParameter('lpf', 'laytyp', $this->soilmodelManager->getLaytyp($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'layavg', $this->soilmodelManager->getLayavg($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'chani', $this->soilmodelManager->getChani($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'layvka', $this->soilmodelManager->getLayvka($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'laywet', $this->soilmodelManager->getLaywet($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'ipakcb', $this->soilmodelManager->getIpakcb($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'hdry', $this->soilmodelManager->getHdry($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'wetfct', $this->soilmodelManager->getWetfct($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'iwetit', $this->soilmodelManager->getIwetit($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'ihdwet', $this->soilmodelManager->getIhdwet($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'hk', $this->soilmodelManager->getHk($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'hani', $this->soilmodelManager->getHani($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'vka', $this->soilmodelManager->getVka($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'ss', $this->soilmodelManager->getSs($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'sy', $this->soilmodelManager->getSy($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'vkcb', $this->soilmodelManager->getVkcb($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'wetdry', $this->soilmodelManager->getWetdry($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'storagecoefficient', $this->soilmodelManager->getStoragecoefficient($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'constantcv', $this->soilmodelManager->getConstantcv($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'thickstrt', $this->soilmodelManager->getThickstrt($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'nocvcorrection', $this->soilmodelManager->getNocvcorrection($soilmodelId));
            $configuration->updatePackageParameter('lpf', 'novfc', $this->soilmodelManager->getNovfc($soilmodelId));
        }

        /*
         * Add PackageDetails for LpfPackage if set
         */
        if ($configuration->flowPackageName() == 'upw') {
            $configuration->updatePackageParameter('upw', 'laytyp', $this->soilmodelManager->getLaytyp($soilmodelId));
            $configuration->updatePackageParameter('upw', 'layavg', $this->soilmodelManager->getLayavg($soilmodelId));
            $configuration->updatePackageParameter('upw', 'chani', $this->soilmodelManager->getChani($soilmodelId));
            $configuration->updatePackageParameter('upw', 'layvka', $this->soilmodelManager->getLayvka($soilmodelId));
            $configuration->updatePackageParameter('upw', 'laywet', $this->soilmodelManager->getLaywet($soilmodelId));
            $configuration->updatePackageParameter('upw', 'ipakcb', $this->soilmodelManager->getIpakcb($soilmodelId));
            $configuration->updatePackageParameter('upw', 'hdry', $this->soilmodelManager->getHdry($soilmodelId));
            $configuration->updatePackageParameter('upw', 'iphdry', $this->soilmodelManager->getIphdry($soilmodelId));
            $configuration->updatePackageParameter('upw', 'hk', $this->soilmodelManager->getHk($soilmodelId));
            $configuration->updatePackageParameter('upw', 'hani', $this->soilmodelManager->getHani($soilmodelId));
            $configuration->updatePackageParameter('upw', 'vka', $this->soilmodelManager->getVka($soilmodelId));
            $configuration->updatePackageParameter('upw', 'ss', $this->soilmodelManager->getSs($soilmodelId));
            $configuration->updatePackageParameter('upw', 'sy', $this->soilmodelManager->getSy($soilmodelId));
            $configuration->updatePackageParameter('upw', 'vkcb', $this->soilmodelManager->getVkcb($soilmodelId));
        }

        /*
         * Add PackageDetails for WelPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, WellBoundary::TYPE) > 0) {
            $welStressPeriodData = $this->modflowModelManager->generateWelStressPeriodData($modflowModelId, $stressPeriods);
            $configuration->updatePackageParameter('wel', 'StressPeriodData', $welStressPeriodData);
        }

        /*
         * Add PackageDetails for RchPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, RechargeBoundary::TYPE) > 0) {
            $rchStressPeriodData = $this->modflowModelManager->generateRchStressPeriodData($modflowModelId, $stressPeriods);
            $configuration->updatePackageParameter('rch', 'StressPeriodData', $rchStressPeriodData);
        }

        /*
         * Add PackageDetails for RivPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, RiverBoundary::TYPE) > 0) {
            $rivStressPeriodData = $this->modflowModelManager->generateRivStressPeriodData($modflowModelId, $stressPeriods);
            $configuration->updatePackageParameter('riv', 'StressPeriodData', $rivStressPeriodData);
        }

        /*
         * Add PackageDetails for GhbPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, GeneralHeadBoundary::TYPE) > 0) {
            $ghbStressPeriodData = $this->modflowModelManager->generateGhbStressPeriodData($modflowModelId, $stressPeriods);
            $configuration->updatePackageParameter('ghb', 'StressPeriodData', $ghbStressPeriodData);
        }

        /*
         * Add PackageDetails for ChdPackage
         */
        if ($this->modflowModelManager->countModelBoundaries($modflowModelId, ConstantHeadBoundary::TYPE) > 0) {
            $chdStressPeriodData = $this->modflowModelManager->generateChdStressPeriodData($modflowModelId, $stressPeriods);
            $configuration->updatePackageParameter('chd', 'StressPeriodData', $chdStressPeriodData);
        }

        return $configuration;
    }

    private function getDefaultValuesWithId(IdInterface $id): ModflowCalculationConfiguration
    {
        return ModflowCalculationConfiguration::createFromDefaultsWithId($id);
    }

    private function getSavedOrDefaultConfigurationById(IdInterface $id): ModflowCalculationConfiguration
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration from %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $id->toString()]
        );

        if (null === $result['configuration']) {
            return $this->getDefaultValuesWithId($id);
        }

        return ModflowCalculationConfiguration::fromJson($result['configuration']);
    }

    private function storeConfiguration(IdInterface $calculationId, ModflowCalculationConfiguration $configuration): void
    {
        $this->connection->update(Table::CALCULATION_CONFIG, array(
            'configuration' => json_encode($configuration),
            'configuration_hash' => md5(json_encode($configuration)),
            'configuration_state' => 0,
            'configuration_response' => ""
        ), array(
            'calculation_id' => $calculationId->toString()
        ));
    }

    private function serialize($object): string
    {
        return base64_encode(serialize($object));
    }

    private function unserialize(string $string)
    {
        return unserialize(base64_decode($string));
    }
}
