<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Exception\ProcessFailedException;
use Inowas\PyprocessingBundle\Model\Modflow\FlopyProcessConfiguration;
use Doctrine\ORM\EntityManagerInterface;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessFactory;

/**
 * Class Flopy
 * @package AppBundle\Service
 *
 * @codeCoverageIgnore
 */
class Flopy
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var string $pyProcessingFolder
     */
    protected $pyProcessingFolder;

    /**
     * Flopy constructor.
     * @param EntityManagerInterface $entityManager
     * @param $pyProcessingFolder
     */
    public function __construct(EntityManagerInterface $entityManager, $pyProcessingFolder){
        $this->entityManager = $entityManager;
        $this->pyProcessingFolder = $pyProcessingFolder;
    }

    /**
     * @param $baseUrl
     * @param $dataFolder
     * @param $modelId
     * @param $apiKey
     * @return bool
     * @throws ProcessFailedException
     */
    public function calculate($baseUrl, $dataFolder, $modelId, $apiKey)
    {
        $process = PythonProcessFactory::create(new FlopyProcessConfiguration($baseUrl, $dataFolder, $modelId, $apiKey));
        $process->getProcess()->setWorkingDirectory($this->pyProcessingFolder);

        $process->getProcess()->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow Calculation Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }

    /**
    public function getRasterResult($modelId, $layer, array $timesteps, array $stressPeriods, $operation = ModflowResultRasterParameter::OP_RAW){

        $process = $this->modflowProcessBuilder->getRasterResultProcess($modelId, $layer, $timesteps, $stressPeriods, $operation);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow RasterResult Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }

    public function getTimeseriesResult($modelId, $layer, $row, $col, $operation = ModflowResultTimeSeriesParameter::OP_RAW){

        $process = $this->modflowProcessBuilder->getTimeseriesResultProcess($modelId, $layer, $row, $col, $operation);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow TimeSeriesResult Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }
    */
}