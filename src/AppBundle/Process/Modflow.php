<?php

namespace AppBundle\Process;

use AppBundle\Model\ModflowProperties\ModflowCalculationProperties;
use AppBundle\Model\ModflowProperties\ModflowRasterResultProperties;
use AppBundle\Model\ModflowProperties\ModflowTimeSeriesResultProperties;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Modflow
{
    const MODFLOW_2005  = "mf2005";
    const MODFLOW_NWT   = "mfnwt";

    /** @var array */
    private $availableExecutables = [self::MODFLOW_2005];
    
    /** @var array */
    protected $configuration;
    

    /** @var  string */
    private $dataFolder;

    /** @var  Serializer */
    protected $serializer;

    /** @var  KernelInterface */
    protected $kernel;

    /** @var  PythonProcess $pythonProcess */
    protected $pythonProcess;

    /** @var string */
    protected $stdOut;

    /**
     * Modflow constructor.
     * @param ModflowProcessConfigurationInterface $configuration
     */
    public function __construct(ModflowProcessConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @param $modelId
     * @param $propertiesJSON
     * @return Process
     */
    private function getResultsProcess($modelId, $propertiesJSON)
    {
        $fs = new Filesystem();
        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        $fs->dumpFile($this->getInputFileName(), $propertiesJSON);
        $scriptName = "modflowResult.py";

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array(
                '-W',
                'ignore',
                $scriptName,
                $this->getBaseUrl(),
                $this->getWorkSpace($modelId),
                $this->getInputFileName(),
                $this->getOutputFileName()))
            ->setWorkingDirectory($this->getWorkingDirectory())
            ->getProcess();

        return $process;
    }

    /**
     * @param $modelId
     * @param string $executable
     * @return string
     * @throws \AppBundle\Exception\ProcessFailedException
     */
    public function calculate()
    {

        $modFlowProcess = new ModflowProcess($this->configuration);
        $modFlowProcess->getProcess();
        
        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array(
                '-W',
                'ignore',
                $scriptName,
                $this->getBaseUrl(),
                $executable,
                $this->getWorkSpace($modelId),
                $this->getInputFileName()))
            ->setWorkingDirectory($this->getWorkingDirectory())
            ->getProcess();

        $process->run();
        if (!$process->isSuccessful()) {
            throw new \AppBundle\Exception\ProcessFailedException();
        }

        $jsonResponse = $process->getOutput();
        $this->stdOut .= $jsonResponse;
        return $this->stdOut;
    }

    /**
     * @param $modelId
     * @param $operation
     * @param int $layer
     * @param array $timesteps
     * @return string
     */
    public function getRasterResult($modelId, $operation, $layer=0, $timesteps=array())
    {
        $this->clear();
        $modflowRasterResultProperties = new ModflowRasterResultProperties($modelId, $layer, $operation);
        foreach ($timesteps as $timestep) {
            $modflowRasterResultProperties->addTimestep($timestep);
        }

        $modflowRasterResultPropertiesJSON = $this->serializer->serialize(
            $modflowRasterResultProperties,
            'json',
            SerializationContext::create()->setGroups(array('modflowProcess'))
        );

        $process = $this->getResultsProcess($modelId, $modflowRasterResultPropertiesJSON);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $jsonResponse = $process->getOutput();
        $this->stdOut .= $jsonResponse;
        return $this->stdOut;
    }

    /**
     * @param $modelId
     * @param $layer
     * @param $row
     * @param $col
     * @param array $timesteps
     * @return string
     */
    public function getTimeseriesResult($modelId, $layer, $row, $col, $timesteps=array())
    {
        $this->clear();

        $modflowTimeSeriesResultProperties = new ModflowTimeSeriesResultProperties($modelId, $layer, $row, $col);
        $modflowTimeSeriesResultProperties->setTimesteps($timesteps);
        $modflowTimeSeriesResultPropertiesJSON = $this->serializer->serialize(
            $modflowTimeSeriesResultProperties,
            'json',
            SerializationContext::create()->setGroups(array('modflowProcess'))
        );

        $process = $this->getResultsProcess($modelId, $modflowTimeSeriesResultPropertiesJSON);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $jsonResponse = $process->getOutput();
        $this->stdOut .= $jsonResponse;
        return $this->stdOut;
    }

    /**
     * @return string
     */
    public function getStdOut()
    {
        return $this->stdOut;
    }
}