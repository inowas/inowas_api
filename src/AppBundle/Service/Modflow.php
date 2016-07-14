<?php

namespace AppBundle\Service;

use AppBundle\Entity\ModflowCalculation;
use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Exception\ProcessFailedException;
use AppBundle\Process\Modflow\ModflowCalculationParameter;
use AppBundle\Process\Modflow\ModflowCalculationProcessConfiguration;
use AppBundle\Process\Modflow\ModflowConfigurationFileCreator;
use AppBundle\Process\Modflow\ModflowResultProcessConfiguration;
use AppBundle\Process\Modflow\ModflowResultRasterParameter;
use AppBundle\Process\Modflow\ModflowResultTimeSeriesParameter;
use AppBundle\Process\PythonProcessFactory;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class Modflow
 * @package AppBundle\Service
 *
 * @codeCoverageIgnore
 */
class Modflow
{
    const MODFLOW_2005  = "mf2005";
    const MODFLOW_NWT   = "mfnwt";

    /** @var array */
    private $availableExecutables = [self::MODFLOW_2005];

    /** @var  string */
    protected $baseUrl;

    /** @var  KernelInterface */
    protected $kernel;

    /**
     * Modflow constructor.
     * @param KernelInterface $kernel
     * @param ConfigurationFileCreatorFactory $configurationFileCreatorFactory
     */
    public function __construct(KernelInterface $kernel, ConfigurationFileCreatorFactory $configurationFileCreatorFactory)
    {
        $this->kernel = $kernel;
        $this->configurationFileCreatorFactory = $configurationFileCreatorFactory;
        $this->baseUrl = $this->kernel->getContainer()->getParameter('inowas.modflow.api_base_url');
        $this->workspace = $this->kernel->getContainer()->getParameter('inowas.modflow.data_folder');
        $this->entityManager = $this->kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    public function addToQueue($modelId, $executable = 'mf2005')
    {
        if (! in_array($executable, $this->availableExecutables)){
            throw new InvalidArgumentException(sprintf('Executable %s is not available.', $executable));
        }

        $modflowCalculation = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
            ->findOneBy(array(
                'modelId' => $modelId,
                'state' => ModflowCalculation::STATE_IN_QUEUE
            ));

        if (! $modflowCalculation instanceof ModflowCalculation){
            $modflowCalculation = new ModflowCalculation();
        }

        $modflowCalculation->setModelId(Uuid::fromString($modelId));
        $modflowCalculation->setExecutable($executable);
        $this->entityManager->persist($modflowCalculation);
        $this->entityManager->flush();
    }

    public function calculate($modelId, $executable = 'mf2005')
    {
        $process = $this->createCalculationProcess($modelId, $executable);
        $process->getProcess()->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow Calculation Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }

    private function createCalculationProcess($modelId, $executable = 'mf2005'){
        if (! in_array($executable, $this->availableExecutables)){
            throw new InvalidArgumentException(sprintf('Executable %s not available.', $executable));
        }

        $mfCalculationParams = new ModflowCalculationParameter($modelId, $this->baseUrl);

        /** @var ModflowConfigurationFileCreator $inputFileCreator */
        $inputFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $inputFileCreator->createFiles($mfCalculationParams);

        $processConfig = new ModflowCalculationProcessConfiguration($inputFileCreator->getInputFile(), $this->workspace.'/'.$modelId, $executable, $this->baseUrl);
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.modflow.working_directory'));
        return PythonProcessFactory::create($processConfig);
    }

    public function getRasterResult($modelId, $layer, array $timesteps, array $stressPeriods, $operation = ModflowResultRasterParameter::OP_RAW){

        $mfResultParams = new ModflowResultRasterParameter($modelId, $layer, $timesteps, $stressPeriods, $operation);

        /** @var ModflowConfigurationFileCreator $configFileCreator*/
        $configFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $configFileCreator->createFiles($mfResultParams);

        $processConfig = new ModflowResultProcessConfiguration(
            $configFileCreator->getInputFile(),
            $configFileCreator->getOutputFile(),
            $this->workspace.'/'.$modelId,
            $this->baseUrl
        );
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.modflow.working_directory'));
        $process = PythonProcessFactory::create($processConfig);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow RasterResult Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }

    public function getTimeseriesResult($modelId, $layer, $row, $col, $operation = ModflowResultTimeSeriesParameter::OP_RAW){

        $mfResultParams = new ModflowResultTimeSeriesParameter($modelId, $layer, $row, $col, $operation);

        /** @var ModflowConfigurationFileCreator $configFileCreator*/
        $configFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $configFileCreator->createFiles($mfResultParams);

        $processConfig = new ModflowResultProcessConfiguration(
            $configFileCreator->getInputFile(),
            $configFileCreator->getOutputFile(),
            $this->workspace.'/'.$modelId,
            $this->baseUrl
        );
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.modflow.working_directory'));
        $process = PythonProcessFactory::create($processConfig);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow TimeSeriesResult Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }
}