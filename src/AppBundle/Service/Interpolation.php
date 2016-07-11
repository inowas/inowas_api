<?php

namespace AppBundle\Service;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Process\InterpolationConfigurationFileCreator;
use AppBundle\Process\InterpolationConfigurationFileCreatorInterface;
use AppBundle\Process\InterpolationParameter;
use AppBundle\Process\InterpolationProcess;
use AppBundle\Process\InterpolationProcessConfiguration;
use AppBundle\Process\InterpolationResult;
use Symfony\Component\HttpKernel\KernelInterface;

class Interpolation
{
    const TYPE_IDW = 'idw';
    const TYPE_MEAN = 'mean';
    const TYPE_GAUSSIAN = 'gaussian';

    /** @var array */
    private $availableTypes = [self::TYPE_MEAN, self::TYPE_GAUSSIAN, self::TYPE_IDW];

    /** @var  InterpolationParameter */
    protected $interpolationConfiguration;

    /** @var InterpolationConfigurationFileCreatorInterface */
    protected $interpolationConfigurationFileCreator;

    /** @var  KernelInterface */
    protected $kernel;

    /**
     * Interpolation constructor.
     * @param $serializer
     * @param $kernel
     */
    public function __construct($serializer, $kernel)
    {
        $this->serializer = $serializer;
        $this->kernel = $kernel;
        $this->interpolationConfigurationFileCreator = new InterpolationConfigurationFileCreator(
            $this->kernel->getContainer()->getParameter('inowas.temp_folder'),
            $this->serializer
        );
    }

    /**
     * @return InterpolationParameter
     */
    public function getInterpolationConfiguration()
    {
        return $this->interpolationConfiguration;
    }

    /**
     * @param InterpolationParameter $interpolationConfiguration
     * @return $this
     */
    public function setInterpolationConfiguration($interpolationConfiguration)
    {
        $this->interpolationConfiguration = $interpolationConfiguration;
        return $this;
    }
    
    /**
     * @param InterpolationConfigurationFileCreatorInterface $interpolationConfigurationFileCreator
     * @return Interpolation
     */
    public function setInterpolationConfigurationFileCreator(InterpolationConfigurationFileCreatorInterface $interpolationConfigurationFileCreator)
    {
        $this->interpolationConfigurationFileCreator = $interpolationConfigurationFileCreator;
        return $this;
    }

    /**
     * @return InterpolationConfigurationFileCreator
     */
    public function getInterpolationConfigurationFileCreator()
    {
        return $this->interpolationConfigurationFileCreator;
    }

    /**
     * @param InterpolationParameter $interpolationParameter
     * @return InterpolationResult|bool
     */
    public function interpolate(InterpolationParameter $interpolationParameter)
    {
        $algorithms = $interpolationParameter->getAlgorithms();
        foreach ($algorithms as $algorithm) {
            if (!in_array($algorithm, $this->availableTypes)) {
                throw new InvalidArgumentException(sprintf('Algorithm %s not found.', $algorithm));
            }
        }

        for ($i = 0; $i < count($algorithms); $i++) {
            $this->interpolationConfigurationFileCreator->createFiles($algorithms[$i], $interpolationParameter);
            $configuration = new InterpolationProcessConfiguration($this->interpolationConfigurationFileCreator);
            $configuration->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.interpolation.working_directory'));
            $process = new InterpolationProcess($configuration);

            if ($process->interpolate())
            {
                $jsonResults = file_get_contents($this->interpolationConfigurationFileCreator->getOutputFile()->getFileName());
                $results = json_decode($jsonResults);

                return new InterpolationResult($results->method, $results->raster, $interpolationParameter->getGridSize(), $interpolationParameter->getBoundingBox());
                break;
            }
        }

        return false;
    }
}
