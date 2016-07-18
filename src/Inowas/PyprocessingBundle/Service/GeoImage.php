<?php

namespace Inowas\PyprocessingBundle\Service;

use Inowas\PyprocessingBundle\Exception\ImageGenerationException;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Exception\ProcessFailedException;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageParameter;
use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageProcessConfiguration;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessFactory;
use Symfony\Component\HttpKernel\KernelInterface;

class GeoImage
{
    const COLOR_RELIEF_GIST_EARTH = 'gist_earth';
    const COLOR_RELIEF_GIST_RAINBOW = 'gist_rainbow';
    const COLOR_RELIEF_JET = 'jet';
    const COLOR_RELIEF_RAINBOW = 'rainbow';
    const COLOR_RELIEF_TERRAIN = 'terrain';

    protected $availableColorReliefs = array(
      self::COLOR_RELIEF_GIST_EARTH,
      self::COLOR_RELIEF_GIST_RAINBOW,
      self::COLOR_RELIEF_JET,
      self::COLOR_RELIEF_RAINBOW,
      self::COLOR_RELIEF_TERRAIN
    );

    const FILE_TYPE_PNG = "png";
    const FILE_TYPE_TIFF = "tiff";

    protected $availableImageFileTypes = array(self::FILE_TYPE_PNG);

    /** @var  KernelInterface */
    protected $kernel;

    /** @var  GeoImageParameter */
    protected $geoImageParameter;

    /** @var ConfigurationFileCreatorFactory */
    protected $configurationFileCreatorFactory;

    /** @var string */
    protected $outputFileName;

    /**
     * GeoImage constructor.
     * @param KernelInterface $kernel
     * @param ConfigurationFileCreatorFactory $configurationFileCreatorFactory
     */
    public function __construct(KernelInterface $kernel, ConfigurationFileCreatorFactory $configurationFileCreatorFactory)
    {
        $this->kernel = $kernel;
        $this->configurationFileCreatorFactory = $configurationFileCreatorFactory;
    }

    public function createImage(GeoImageParameter $geoImageParameter){

        if (! in_array($geoImageParameter->getColorRelief(), $this->availableColorReliefs)){
            throw new InvalidArgumentException(sprintf('ColorRelief %s is unknown.', $geoImageParameter->getColorRelief()));
        }

        if (! in_array($geoImageParameter->getFileFormat(), $this->availableImageFileTypes)){
            throw new InvalidArgumentException(sprintf('FileFormat %s is unknown.', $geoImageParameter->getFileFormat()));
        }

        $geoImageConfigurationFileCreator = $this->configurationFileCreatorFactory->create('geoimage');
        $geoImageConfigurationFileCreator->createFiles($geoImageParameter);

        $inputFile = $geoImageConfigurationFileCreator->getInputFile();
        $outputFile = $geoImageConfigurationFileCreator->getOutputFile();

        $configuration = new GeoImageProcessConfiguration($inputFile, $outputFile);
        $configuration->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing.directory'));
        $process = PythonProcessFactory::create($configuration);

        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException('GeoImage-Process has failed.');
        }

        $response = json_decode($process->getOutput());

        if (isset($response->error)) {
            throw new ImageGenerationException('Error in geoimage-generation');
        }

        $this->outputFileName = $geoImageConfigurationFileCreator->getOutputFile()->getFileName();

        return true;
    }

    public function getOutputFileName(){
        return $this->outputFileName;
    }
}