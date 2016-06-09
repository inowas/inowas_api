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

    /** @var string  */
    private $tmpFolder = '/tmp/modflow';

    /** @var string  */
    private $tmpFileName = '';

    /** @var  string */
    private $dataFolder;

    /** @var array $data */
    protected $data;

    /** @var string $method */
    protected $method;

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
     * @param $dataFolder
     * @param $baseUrl
     */
    public function __construct($serializer, $kernel, $pythonProcess, $dataFolder, $baseUrl)
    {
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->pythonProcess = $pythonProcess;
        $this->dataFolder = $dataFolder;
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

    public function getWorkSpace($modelId)
    {
        return $this->kernel->getRootDir().'/'.$this->dataFolder.'/'.$modelId;
    }

    public function clear()
    {
        $this->stdOut = "";
        $this->tmpFileName = Uuid::uuid4()->toString();
    }

    public function calculate($modelId, $executable=self::MODFLOW_2005)
    {
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

        $inputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.in';
        $fs->dumpFile($inputFileName, $modflowCalculationPropertiesJSON);

        $scriptName = "modflowCalculation.py";
        $workspace = $this->getWorkSpace($modelId);

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $this->getBaseUrl(), $executable, $workspace, $inputFileName))
            ->setWorkingDirectory($this->kernel->getRootDir() . '/../py/pyprocessing/modflow')
            ->getProcess();

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $jsonResponse = $process->getOutput();
        $this->stdOut .= $jsonResponse;
        return $this->stdOut;
    }

    public function getRasterResult($modelId, $operation, $layer=0, $timesteps=array())
    {
        $modflowRasterResultProperties = new ModflowRasterResultProperties($modelId, $layer, $operation);
        $modflowRasterResultProperties->setTimesteps($timesteps);
        $modflowRasterResultPropertiesJSON = $this->serializer->serialize(
            $modflowRasterResultProperties,
            'json',
            SerializationContext::create()->setGroups(array('modflowProcess'))
        );

        $fs = new Filesystem();
        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        $inputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.in';
        $outputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.out';
        $fs->dumpFile($inputFileName, $modflowRasterResultPropertiesJSON);

        $scriptName = "modflowResult.py";
        $workspace = $this->getWorkSpace($modelId);

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $this->getBaseUrl(), $workspace, $inputFileName, $outputFileName))
            ->setWorkingDirectory($this->kernel->getRootDir() . '/../py/pyprocessing/modflow')
            ->getProcess();

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $jsonResponse = $process->getOutput();
        $this->stdOut .= $jsonResponse;
        return $this->stdOut;
    }

    public function getTimeseriesResult($modelId, $layer, $row, $col, $timesteps=array())
    {
        $modflowTimeSeriesResultProperties = new ModflowTimeSeriesResultProperties($modelId, $layer, $row, $col);
        $modflowTimeSeriesResultProperties->setTimesteps($timesteps);
        $modflowTimeSeriesResultPropertiesJSON = $this->serializer->serialize(
            $modflowTimeSeriesResultProperties,
            'json',
            SerializationContext::create()->setGroups(array('modflowProcess'))
        );

        $fs = new Filesystem();
        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        $inputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.in';
        $outputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.out';
        $fs->dumpFile($inputFileName, $modflowTimeSeriesResultPropertiesJSON);

        $scriptName = "modflowResult.py";
        $workspace = $this->getWorkSpace($modelId);

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $this->getBaseUrl(), $workspace, $inputFileName, $outputFileName))
            ->setWorkingDirectory($this->kernel->getRootDir() . '/../py/pyprocessing/modflow')
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
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getStdOut()
    {
        return $this->stdOut;
    }
}