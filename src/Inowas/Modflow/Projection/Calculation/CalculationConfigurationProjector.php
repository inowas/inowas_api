<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\Calculation;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\Ibound;
use Inowas\Common\Modflow\Strt;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\CalculationWasCreated;
use Inowas\Modflow\Model\Packages\Packages;
use Inowas\Modflow\Model\Service\ModflowModelManager;
use Inowas\Modflow\Model\Service\ModflowModelManagerInterface;
use Inowas\Modflow\Model\Service\SoilmodelManagerInterface;
use Inowas\Modflow\Projection\Table;
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
        $table->addColumn('configuration', 'text');
        $table->setPrimaryKey(['calculation_id', 'modflow_model_id']);
    }

    public function onCalculationWasCreated(CalculationWasCreated $event): void
    {
        $packages = $this->getDefaultValues();
        $packages->updateStartDateTime($event->start());
        $packages->updateTimeUnit($event->timeUnit());
        $packages->updateLengthUnit($event->lengthUnit());

        /*
         * Add PackageDetails for DisPackage
         * Grid Properties
         */
        $gridSize = $this->modflowModelManager->getGridSize($event->modflowModelId());
        $boundingBox = $this->modflowModelManager->getBoundingBox($event->modflowModelId());
        $packages->updateGridParameters($gridSize, $boundingBox);

        /*
         * Add PackageDetails for DisPackage
         * Layers and Elevations
         */
        $packages->updatePackageParameter('dis', 'nlay', $this->soilmodelManager->getNlay($event->soilModelId()));
        $packages->updatePackageParameter('dis', 'top', $this->soilmodelManager->getTop($event->soilModelId()));
        $packages->updatePackageParameter('dis', 'botm', $this->soilmodelManager->getBotm($event->soilModelId()));

        /*
         * Add PackageDetails for DisPackage
         * StressPeriods
         */
        $stressPeriods = $this->modflowModelManager->getStressPeriods($event->modflowModelId(), $event->start(), $event->end());
        $packages->updatePackageParameter('dis', 'perlen', $stressPeriods->perlen());
        $packages->updatePackageParameter('dis', 'nstp', $stressPeriods->nstp());
        $packages->updatePackageParameter('dis', 'tsmult', $stressPeriods->tsmult());
        $packages->updatePackageParameter('dis', 'steady', $stressPeriods->steady());
        $packages->updatePackageParameter('dis', 'nper', $stressPeriods->nper());

        /*
         * Add PackageDetails for BasPackage
         * Ibound, Strt
        */
        $activeCells = $this->modflowModelManager->getAreaActiveCells($event->modflowModelId());
        $iBound = Ibound::fromActiveCellsAndNumberOfLayers($activeCells, $this->soilmodelManager->getNlay($event->soilModelId())->toInteger());
        $packages->updatePackageParameter('bas', 'ibound', $iBound);
        $strt = Strt::fromTopAndNumberOfLayers($this->soilmodelManager->getTop($event->soilModelId()), $this->soilmodelManager->getNlay($event->soilModelId())->toInteger());
        $packages->updatePackageParameter('bas', 'strt', $strt);

        /*
         * Add PackageDetails for LpfPackage
         * LayTyp
        */
        $laytyp = $this->soilmodelManager->getLaytyp($event->soilModelId());
        $packages->updatePackageParameter('lpf', 'laytyp', $laytyp);

        $this->connection->insert(Table::CALCULATION_CONFIG, array(
            'calculation_id' => $event->calculationId()->toString(),
            'modflow_model_id' => $event->modflowModelId()->toString(),
            'soilmodel_id' => $event->soilModelId()->toString(),
            'user_id' => $event->userId()->toString(),
            'configuration' => json_encode($packages)
        ));
    }


    private function getConfigByCalculationId(ModflowId $calculationId): ?Packages
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration FROM %s WHERE calculation_id = :calculation_id', Table::CALCULATION_CONFIG),
            ['calculation_id' => $calculationId->toString()]
        );

        if ($result){
            return Packages::fromJson($result['configuration']);
        }

        return null;
    }

    private function getConfigByModelId(ModflowId $modelId): ?Packages
    {
        $result = $this->connection->fetchAssoc(
            sprintf('SELECT configuration FROM %s WHERE modflow_model_id = :modflow_model_id', Table::CALCULATION_CONFIG),
            ['modflow_model_id' => $modelId->toString()]
        );

        if ($result){
            return Packages::fromJson($result['configuration']);
        }

        return null;
    }

    private function getDefaultValues(): Packages
    {
        return Packages::createFromDefaults();
    }
}
