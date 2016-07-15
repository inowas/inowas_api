<?php

namespace Inowas\ModflowBundle\Service;

use AppBundle\Process\Modflow\ModflowCalculationParameter;
use AppBundle\Process\Modflow\ModflowCalculationProcessConfiguration;
use AppBundle\Process\Modflow\ModflowConfigurationFileCreator;
use AppBundle\Process\Modflow\ModflowResultProcessConfiguration;
use AppBundle\Process\Modflow\ModflowResultRasterParameter;
use AppBundle\Process\Modflow\ModflowResultTimeSeriesParameter;
use AppBundle\Process\PythonProcessFactory;
use AppBundle\Service\ConfigurationFileCreatorFactory;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class Modflow
 * @package AppBundle\Service
 *
 * @codeCoverageIgnore
 */
class ModflowProcessBuilder
{
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
        $this->baseUrl = $this->kernel->getContainer()->getParameter('inowas.api_base_url');
        $this->workspace = $this->kernel->getContainer()->getParameter('inowas.modflow.data_folder');
    }

    public function buildCalculationProcess($modelId, $executable = 'mf2005'){

        $mfCalculationParams = new ModflowCalculationParameter($modelId, $this->baseUrl);

        /** @var ModflowConfigurationFileCreator $inputFileCreator */
        $inputFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $inputFileCreator->createFiles($mfCalculationParams);

        $processConfig = new ModflowCalculationProcessConfiguration($inputFileCreator->getInputFile(), $this->workspace.'/'.$modelId, $executable, $this->baseUrl);
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing.directory'));
        return PythonProcessFactory::create($processConfig);
    }

    public function buildRasterResultProcess($modelId, $layer, $timesteps, $stressPeriods, $operation){
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
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing.directory'));
        return PythonProcessFactory::create($processConfig);
    }

    public function buildTimeseriesResultProcess($modelId, $layer, $row, $col, $operation){
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
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing.directory'));
        return PythonProcessFactory::create($processConfig);
    }
}