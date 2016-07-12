<?php

namespace AppBundle\Service;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Process\GeoImage\GeoImageConfigurationFileCreator;
use AppBundle\Process\Interpolation\InterpolationConfigurationFileCreator;
use AppBundle\Process\Modflow\ModflowCalculationConfigurationFileCreator;
use JMS\Serializer\Serializer;
use Symfony\Component\HttpKernel\KernelInterface;

class ConfigurationFileCreatorFactory
{

    /** @var  Serializer */
    protected $serializer;

    /** @var  KernelInterface */
    protected $kernel;

    /**
     * InterpolationConfigurationFileCreator constructor.
     * @param $kernel
     * @param $serializer
     */
    public function __construct(KernelInterface $kernel, $serializer)
    {
        $this->kernel = $kernel;
        $this->serializer = $serializer;
    }

    /**
     * @param $type
     * @return GeoImageConfigurationFileCreator|InterpolationConfigurationFileCreator
     */
    public function create($type){
        switch ($type) {
            case 'interpolation':
                return new InterpolationConfigurationFileCreator($this->kernel, $this->serializer);
            break;

            case 'geoimage':
                return new GeoImageConfigurationFileCreator($this->kernel, $this->serializer);
            break;

            case 'modflow.calculation':
                return new ModflowCalculationConfigurationFileCreator($this->kernel);
            break;
        }

        throw new InvalidArgumentException(sprintf('Unknown type of ConfigurationFileCreator, given type is %s.', $type));
    }
}