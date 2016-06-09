<?php

namespace AppBundle\Service;

use AppBundle\Model\ModflowProcess\ModflowCalculationProperties;
use Doctrine\Common\Collections\ArrayCollection;
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

    /** @var string  */
    private $tmpFolder = '/tmp/modflow';

    /** @var string  */
    private $tmpFileName = '';

    /** @var string  */
    private $workspace = '';

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
     * Interpolation constructor.
     * @param $serializer
     * @param $kernel
     * @param $pythonProcess
     */
    public function __construct($serializer, $kernel, $pythonProcess)
    {
        $this->points = new ArrayCollection();
        $this->pythonProcess = $pythonProcess;
        $this->kernel = $kernel;
        $this->serializer = $serializer;
        $this->stdOut = '';
        $this->tmpFileName = Uuid::uuid4()->toString();
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
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @param string $workspace
     * @return Modflow
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
        return $this;
    }



    public function clear()
    {
        $this->data = null;
        $this->method = null;
        $this->points = new ArrayCollection();
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
        $outputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.out';

        $scriptName = "modflowCalculation.py";
        $workspace = "../data/modflow/".$modelId;
        $baseUrl = "http://localhost:8090";

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $baseUrl, $executable, $workspace, $inputFileName))
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

    public function getResult()
    {
        
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