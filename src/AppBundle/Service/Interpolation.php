<?php

namespace AppBundle\Service;

use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Process\InterpolationParameter;
use AppBundle\Process\PythonProcessFactory;
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

    /** @var ConfigurationFileCreatorFactory */
    protected $interpolationConfigurationFileCreator;

    /** @var  KernelInterface */
    protected $kernel;

    /**
     * Interpolation constructor.
     * @param $kernel
     * @param ConfigurationFileCreatorFactory $interpolationConfigurationFileCreator
     */
    public function __construct(KernelInterface $kernel, ConfigurationFileCreatorFactory $interpolationConfigurationFileCreator)
    {
        $this->kernel = $kernel;
        $this->interpolationConfigurationFileCreator = $interpolationConfigurationFileCreator;
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
            $process = PythonProcessFactory::create($configuration);
            $process->run();
            if ($process->isSuccessful())
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
