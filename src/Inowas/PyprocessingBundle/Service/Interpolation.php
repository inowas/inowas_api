<?php

namespace Inowas\PyprocessingBundle\Service;

use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationConfiguration;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessFactory;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationProcessConfiguration;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationResult;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;

class Interpolation
{
    const TYPE_IDW = 'idw';
    const TYPE_MEAN = 'mean';
    const TYPE_GAUSSIAN = 'gaussian';

    /** @var array */
    private $availableTypes = [self::TYPE_MEAN, self::TYPE_GAUSSIAN, self::TYPE_IDW];

    /** @var  InterpolationConfiguration */
    protected $interpolationConfiguration;

    /** @var ConfigurationFileCreatorFactory */
    protected $configurationFileCreatorFactory;

    /** @var  KernelInterface */
    protected $kernel;

    /**
     * Interpolation constructor.
     * @param KernelInterface $kernel
     * @param ConfigurationFileCreatorFactory $configurationFileCreatorFactory
     */
    public function __construct(KernelInterface $kernel, ConfigurationFileCreatorFactory $configurationFileCreatorFactory)
    {
        $this->kernel = $kernel;
        $this->configurationFileCreatorFactory = $configurationFileCreatorFactory;
    }

    /**
     * @param InterpolationConfiguration $interpolationParameter
     * @return \Inowas\PyprocessingBundle\Model\Interpolation\InterpolationResult|bool
     */
    public function interpolate(InterpolationConfiguration $interpolationParameter)
    {
        $algorithms = $interpolationParameter->getAlgorithms();
        foreach ($algorithms as $algorithm) {
            if (!in_array($algorithm, $this->availableTypes)) {
                throw new InvalidArgumentException(sprintf('Algorithm %s not found.', $algorithm));
            }
        }

        $ni = count($algorithms);
        for ($i = 0; $i < $ni; $i++) {
            $fileCreator = $this->configurationFileCreatorFactory->create('interpolation');
            $fileCreator->createFiles($algorithms[$i], $interpolationParameter);

            $configuration = new InterpolationProcessConfiguration($fileCreator);
            $configuration->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.pyprocessing_folder'));

            $process = PythonProcessFactory::create($configuration);

            $process->run();
            if ($process->isSuccessful())
            {
                $jsonResults = file_get_contents($fileCreator->getOutputFile()->getFileName());
                $results = json_decode($jsonResults);

                return new InterpolationResult($results->method, $results->raster, $interpolationParameter->getGridSize(), $interpolationParameter->getBoundingBox());
                break;
            } else {
                echo $process->getErrorOutput();
            }
        }

        return false;
    }
}
