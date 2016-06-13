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
    /**
        aspect: aspect oriented grey colors
        aspectcolr: aspect oriented rainbow colors
        bcyr: blue through cyan through yellow to red
        bgyr: blue through green through yellow to red
        byg: blue through yellow to green
        byr: blue through yellow to red
        celsius: blue to red for degree Celsius temperature
        corine: EU Corine land cover colors
        curvature: for terrain curvatures (from v.surf.rst and r.slope.aspect)
        differences: differences oriented colors
        elevation: maps relative ranges of raster values to elevation color ramp
        etopo2: colors for ETOPO2 worldwide bathymetry/topography
        evi: enhanced vegetative index colors
        gdd: accumulated growing degree days
        grey: grey scale
        grey.eq: histogram-equalized grey scale
        grey.log: histogram logarithmic transformed grey scale
        grey1.0: grey scale for raster values between 0.0-1.0
        grey255: grey scale for raster values between 0-255
        gyr: green through yellow to red
        haxby: relative colors for bathymetry or topography
        ndvi: Normalized Difference Vegetation Index colors
        population: color table covering human population classification breaks
        population_dens: color table covering human population density classification breaks
        precipitation: precipitation color table (0..2000mm)
        precipitation_monthly: precipitation color table (0..1000mm)
        rainbow: rainbow color table
        ramp: color ramp
        random: random color table
        rstcurv: terrain curvature (from r.resamp.rst)
        rules: create new color table based on user-specified rules read from stdin
        ryb: red through yellow to blue
        ryg: red through yellow to green
        sepia: yellowish-brown through to white
        slope: r.slope.aspect-type slope colors for raster values 0-90
        srtm: color palette for Shuttle Radar Topography Mission elevation
        terrain: global elevation color table covering -11000 to +8850m
        wave: color wave
     */

    const COLOR_RELIEF_GIST_EARTH = 'gist_earth';
    const COLOR_RELIEF_JET = 'jet';
    const COLOR_RELIEF_RAINBOW = 'rainbow';
    const COLOR_RELIEF_TERRAIN = 'terrain';

    protected $available_color_reliefs = array(
      self::COLOR_RELIEF_GIST_EARTH,
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

    public function createImageFromRaster(Raster $raster, $fileFormat="png", $colorRelief=self::COLOR_RELIEF_GIST_EARTH, $targetProjection=4326)
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
        
        $geoTiffProperties = new GeoImageProperties($raster,  $colorRelief, $targetProjection, $fileFormat);
        $geoTiffPropertiesJSON = $this->serializer->serialize(
            $geoTiffProperties,
            'json',
            SerializationContext::create()->setGroups(array("geoimage"))
        );

        $fs = new Filesystem();
        if (!$fs->exists($this->tmpFolder)) {
            $fs->mkdir($this->tmpFolder);
        }

        $this->tmpFileName = Uuid::uuid4()->toString();
        $inputFileName = $this->tmpFolder . '/' . $this->tmpFileName . '.in';
        $fs->dumpFile($inputFileName, $geoTiffPropertiesJSON);
        $this->outputFileName = $this->dataFolder.'/'.$raster->getId()->toString();

        $scriptName = "geoImageCreator.py";

        /** @var Process $process */
        $process = $this->pythonProcess
            ->setArguments(array('-W', 'ignore', $scriptName, $inputFileName, $this->outputFileName))
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