<?php

namespace AppBundle\Service;

use AppBundle\Entity\Raster;
use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Model\GeoImage\GeoImageProperties;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class GeoImage
{
    const COLOR_RELIEF_GIST_EARTH = 'gist_earth';
    const COLOR_RELIEF_GIST_RAINBOW = 'gist_rainbow';
    const COLOR_RELIEF_JET = 'jet';
    const COLOR_RELIEF_RAINBOW = 'rainbow';
    const COLOR_RELIEF_TERRAIN = 'terrain';

    protected $available_color_reliefs = array(
      self::COLOR_RELIEF_GIST_EARTH,
      self::COLOR_RELIEF_GIST_RAINBOW,
      self::COLOR_RELIEF_JET,
      self::COLOR_RELIEF_RAINBOW,
      self::COLOR_RELIEF_TERRAIN
    );

    const FILE_TYPE_PNG = "png";
    const FILE_TYPE_TIFF = "tiff";

    protected $available_imageFileTypes = array(self::FILE_TYPE_PNG);

    /** @var Serializer $serializer */
    protected $serializer;

    /** @var  KernelInterface */
    protected $kernel;

    /** @var  PythonProcess $pythonProcess */
    protected $pythonProcess;

    /** @var  string $workingDirectory */
    protected $workingDirectory;

    /** @var  string $dataFolder */
    protected $dataFolder;

    /** @var  string $tmpFolder */
    protected $tmpFolder;

    /** @var  string $tmpFileName */
    protected $tmpFileName;

    /** @var  string $outputFileName */
    protected $outputFileName;

    /** @var string */
    protected $stdOut;

    /**
     * GeoTiff constructor.
     * @param Serializer $serializer
     * @param KernelInterface $kernel
     * @param PythonProcess $pythonProcess
     * @param $workingDirectory
     * @param $dataFolder
     * @param $tmpFolder
     */
    public function __construct(
        Serializer $serializer,
        KernelInterface $kernel,
        PythonProcess $pythonProcess,
        $workingDirectory,
        $dataFolder,
        $tmpFolder
    ){
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->pythonProcess = $pythonProcess;
        $this->workingDirectory = $workingDirectory;
        $this->dataFolder = $dataFolder;
        $this->tmpFolder = $tmpFolder;
    }

    public function createImageFromRaster(Raster $raster, $activeCells=null, $fileFormat="png", $colorRelief=self::COLOR_RELIEF_JET, $targetProjection=4326)
    {

        if (!$raster->getBoundingBox() instanceof BoundingBox) {
            throw new InvalidArgumentException('Raster has no valid BoundingBox-Element');
        }

        if (!$raster->getGridSize() instanceof GridSize) {
            throw new InvalidArgumentException('Raster has no valid Gridsize-Element');
        }

        if (!count($raster->getData()) == $raster->getGridSize()->getNY()){
            throw new InvalidArgumentException(sprintf('RasterData rowCount differs from GridSize rowCount', count($raster->getData()), $raster->getGridSize()->getNY()));
        }

        if (!count($raster->getData()[0]) == $raster->getGridSize()->getNX()){
            throw new InvalidArgumentException(sprintf('RasterData colCount differs from GridSize colCount', count($raster->getData()[0]), $raster->getGridSize()->getNX()));
        }

        if (!in_array($colorRelief, $this->available_color_reliefs)){
            throw new InvalidArgumentException('Given color-relief is not available');
        }

        if (!in_array($fileFormat, $this->available_imageFileTypes)){
            throw new InvalidArgumentException(sprintf('Given fileFormat %s is not supported.', $fileFormat));
        }

        $outputFileName = $this->dataFolder.'/'.$raster->getId()->toString();
        $this->outputFileName = $outputFileName.'.'.$fileFormat;

        $fs = new Filesystem();
        if (!$fs->exists($this->dataFolder)) {
            $fs->mkdir($this->dataFolder);
        }

        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        if ($fs->exists($this->outputFileName)){
            return "File exists already";
        }

        $geoTiffProperties = new GeoImageProperties($raster, $activeCells, $colorRelief, $targetProjection, $fileFormat);
        $geoTiffPropertiesJSON = $this->serializer->serialize(
            $geoTiffProperties,
            'json',
            SerializationContext::create()->setGroups(array("geoimage"))
        );
        
        $this->tmpFileName = Uuid::uuid4()->toString();
        $inputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.in';
        $fs->dumpFile($inputFileName, $geoTiffPropertiesJSON);
        $scriptName = "geoImageCreator.py";

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $inputFileName, $outputFileName))
            ->setWorkingDirectory($this->workingDirectory)
            ->getProcess();

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $response = json_decode($process->getOutput());

        if (isset($response->error)) {
            throw new \Exception('Error in geotiff-generation');
        }

        if (isset($response->success)) {
            $this->stdOut .= $response->success;
        }

        return $this->stdOut;
    }

    /**
     * @return string
     */
    public function getOutputFileName()
    {
        return $this->outputFileName;
    }

    /**
     * @return string
     */
    public function getStdOut()
    {
        return $this->stdOut;
    }
}