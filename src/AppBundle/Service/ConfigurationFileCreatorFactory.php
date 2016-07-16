<?php

namespace AppBundle\Service;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Process\GeoImage\GeoImageConfigurationFileCreator;
use AppBundle\Process\Interpolation\InterpolationConfigurationFileCreator;
use Inowas\ModflowBundle\Model\ModflowConfigurationFileCreator;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\KernelInterface;

class ConfigurationFileCreatorFactory
{

    /** @var  Serializer */
    protected $serializer;

    /** @var  KernelInterface */
    protected $kernel;

    /** @var string */
    protected $tempFolder;

    /**
     * InterpolationConfigurationFileCreator constructor.
     * @param $kernel
     * @param $serializer
     */
    public function __construct(KernelInterface $kernel, $serializer)
    {
        $this->kernel = $kernel;
        $this->serializer = $serializer;
        $this->tempFolder = $this->kernel->getContainer()->getParameter('inowas.temp_folder');
    }

    /**
     * @param $type
     * @return GeoImageConfigurationFileCreator|InterpolationConfigurationFileCreator|\Inowas\ModflowBundle\Model\ModflowConfigurationFileCreator
     */
    public function create($type){
        switch ($type) {
            case 'interpolation':
                return new InterpolationConfigurationFileCreator($this->kernel, $this->serializer);
            break;

            case 'geoimage':
                return new GeoImageConfigurationFileCreator(
                    $this->tempFolder,
                    $this->kernel->getContainer()->getParameter('inowas.geotiff.data_folder')
                );
            break;

            case 'modflow':
                return new ModflowConfigurationFileCreator(
                    $this->tempFolder
                );
            break;
        }

        throw new InvalidArgumentException(sprintf('Unknown type of ConfigurationFileCreator, given type is %s.', $type));
    }
}