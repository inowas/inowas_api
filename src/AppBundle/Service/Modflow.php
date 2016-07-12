<?php

namespace AppBundle\Service;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Exception\ProcessFailedException;
use AppBundle\Process\Modflow\ModflowCalculationConfigurationFileCreator;
use AppBundle\Process\Modflow\ModflowCalculationParameter;
use AppBundle\Process\Modflow\ModflowCalculationProcessConfiguration;
use AppBundle\Process\PythonProcessFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class Modflow
{
    const MODFLOW_2005  = "mf2005";
    const MODFLOW_NWT   = "mfnwt";

    /** @var array */
    private $availableExecutables = [self::MODFLOW_2005];

    /** @var  string */
    protected $baseUrl;

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
    }

    public function calculate($modelId, $executable = 'mf2005'){
        if (! in_array($executable, $this->availableExecutables)){
            throw new InvalidArgumentException(sprintf('Executable %s not available.', $executable));
        }

        /** @var ModflowCalculationConfigurationFileCreator $inputFileCreator */
        $inputFileCreator = $this->configurationFileCreatorFactory->create('modflow.calculation');
        $inputFileCreator->createFiles(new ModflowCalculationParameter($modelId, $this->baseUrl));
        $processConfig = new ModflowCalculationProcessConfiguration($inputFileCreator->getInputFile(), $this->workspace.'/'.$modelId, $executable, $this->baseUrl);
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.modflow.working_directory'));
        $process = PythonProcessFactory::create($processConfig);
        $process->run();
        dump($process->getProcess()->getCommandLine());
        dump($process->getProcess()->getErrorOutput());
        dump($process->getProcess()->getOutput());
        if (! $process->isSuccessful())
        {
            throw new ProcessFailedException('Process failed ;(');
        }

        return true;
    }
}