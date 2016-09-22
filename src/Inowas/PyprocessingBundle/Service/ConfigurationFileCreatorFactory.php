<?php

namespace Inowas\PyprocessingBundle\Service;

use Inowas\PyprocessingBundle\Model\GeoImage\GeoImageConfigurationFileCreator;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationConfigurationFileCreator;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator;
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
     * @return GeoImageConfigurationFileCreator|InterpolationConfigurationFileCreator|\Inowas\PyprocessingBundle\Model\Modflow\ModflowConfigurationFileCreator
     */
    public function create($type){
        switch ($type) {
            case 'interpolation':
                return new InterpolationConfigurationFileCreator(
                    $this->tempFolder
                );
            break;

            case 'geoimage':
                return new GeoImageConfigurationFileCreator(
                    $this->tempFolder,
                    $this->kernel->getContainer()->getParameter('inowas.geoimage.data_folder')
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
