<?php

namespace Inowas\PyprocessingBundle\Service;

use Inowas\PyprocessingBundle\Model\Modflow\ModflowCalculationParameter;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowCalculationProcessConfiguration;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultProcessConfiguration;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultRasterParameter;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowResultTimeSeriesParameter;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessFactory;
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

        /** @var \Inowas\PyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator $inputFileCreator */
        $inputFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $inputFileCreator->createFiles($mfCalculationParams);

        $processConfig = new ModflowCalculationProcessConfiguration($inputFileCreator->getInputFile(), $this->workspace.'/'.$modelId, $executable, $this->baseUrl);
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing_folder'));
        return PythonProcessFactory::create($processConfig);
    }

    public function buildRasterResultProcess($modelId, $layer, $timesteps, $stressPeriods, $operation){
        $mfResultParams = new ModflowResultRasterParameter($modelId, $layer, $timesteps, $stressPeriods, $operation);

        /** @var \Inowas\PyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator $configFileCreator*/
        $configFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $configFileCreator->createFiles($mfResultParams);

        $processConfig = new ModflowResultProcessConfiguration(
            $configFileCreator->getInputFile(),
            $configFileCreator->getOutputFile(),
            $this->workspace.'/'.$modelId,
            $this->baseUrl
        );
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing_folder'));
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
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing_folder'));
        return PythonProcessFactory::create($processConfig);
    }
}