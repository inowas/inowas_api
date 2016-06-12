<?php

namespace AppBundle\Service;

use AppBundle\Model\ModflowProperties\ModflowCalculationProperties;
use AppBundle\Model\ModflowProperties\ModflowRasterResultProperties;
use AppBundle\Model\ModflowProperties\ModflowTimeSeriesResultProperties;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class Modflow
{
    const MODFLOW_2005  = "mf2005";
    const MODFLOW_NWT   = "mfnwt";

    /** @var array */
    private $availableExecutables = [self::MODFLOW_2005];

    /** @var string */
    private $baseUrl = "http://localhost/";

    /** @var string $workingDirectory */
    private $workingDirectory;

    /** @var string  */
    private $tmpFolder;

    /** @var string  */
    private $tmpFileName = '';

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
     * @param $serializer
     * @param $kernel
     * @param $pythonProcess
     * @param $workingDirectory
     * @param $dataFolder
     * @param $tmpFolder
     * @param $baseUrl
     */
    public function __construct(
        Serializer $serializer,
        KernelInterface $kernel,
        PythonProcess $pythonProcess,
        $workingDirectory,
        $dataFolder,
        $tmpFolder,
        $baseUrl
    ){
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->pythonProcess = $pythonProcess;
        $this->workingDirectory = $workingDirectory;
        $this->dataFolder = $dataFolder;
        $this->tmpFolder = $tmpFolder;
        $this->baseUrl = $baseUrl;

        $this->stdOut = '';
        $this->tmpFileName = Uuid::uuid4()->toString();
    }

    /**
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * @return string
     */
    public function getTmpFolder()
    {
        return $this->tmpFolder;
    }

    /**
     * @param string $tmpFolder
     * @return Interpolation
     */
    public function setTmpFolder($tmpFolder)
    {
        $this->tmpFolder = $tmpFolder;
        return $this;
    }

    /**
     * @return string
     */
    public function getTmpFileName()
    {
        return $this->tmpFileName;
    }

    /**
     * @param string $tmpFileName
     * @return Interpolation
     */
    public function setTmpFileName($tmpFileName)
    {
        $this->tmpFileName = $tmpFileName;
        return $this;
    }

    /**
     * @return string
     */
    public function getDataFolder()
    {
        return $this->dataFolder;
    }

    /**
     * @return string
     */
    public function getWorkingDirectory()
    {
        return $this->kernel->getRootDir() . '/../py/pyprocessing/modflow';
    }

    /**
     * @param $modelId
     * @return string
     */
    public function getWorkSpace($modelId)
    {
        return $this->dataFolder.'/'.$modelId;
    }

    /**
     * @return string
     */
    private function getInputFileName()
    {
        return $this->tmpFolder . '/' . $this->tmpFileName . '.in';
    }

    /**
     * @return string
     */
    private function getOutputFileName()
    {
        return $this->tmpFolder . '/' . $this->tmpFileName . '.out';
    }

    private function clear()
    {
        $this->stdOut = "";
        $this->tmpFileName = Uuid::uuid4()->toString();
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
     */
    public function calculate($modelId, $executable=self::MODFLOW_2005)
    {
        $this->clear();
        if (!in_array($executable, $this->availableExecutables)) {
            throw new NotFoundHttpException();
        }

        $modflowCalculationProperties = new ModflowCalculationProperties($modelId);
        $modflowCalculationPropertiesJSON = $this->serializer->serialize(
            $modflowCalculationProperties,
            'json',
            SerializationContext::create()->setGroups(array('modflowProcess'))
        );

        $fs = new Filesystem();
        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        $fs->dumpFile($this->getInputFileName(), $modflowCalculationPropertiesJSON);

        $scriptName = "modflowCalculation.py";

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
            throw new ProcessFailedException($process);
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
        $modflowRasterResultProperties->setTimesteps($timesteps);
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