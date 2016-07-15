<?php

namespace Inowas\ModflowBundle\Service;

use AppBundle\Entity\ModflowCalculation;
use AppBundle\Exception\InvalidArgumentException;
use AppBundle\Exception\ProcessFailedException;
use Inowas\ModflowBundle\Model\ModflowResultRasterParameter;
use AppBundle\Process\Modflow\ModflowResultTimeSeriesParameter;
use Inowas\ModflowBundle\Service\ModflowProcessBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Ramsey\Uuid\Uuid;

/**
 * Class Modflow
 * @package AppBundle\Service
 *
 * @codeCoverageIgnore
 */
class Modflow
{
    const MODFLOW_2005  = "mf2005";
    const MODFLOW_NWT   = "mfnwt";

    /** @var array */
    private $availableExecutables = [self::MODFLOW_2005];


    /** @var EntityManagerInterface  */
    protected $entityManager;

    /** @var ModflowProcessBuilder  */
    protected $modflowProcessBuilder;


    public function __construct(EntityManagerInterface $entityManager, ModflowProcessBuilder $modflowProcessBuilder)
    {
        $this->entityManager = $entityManager;
        $this->modflowProcessBuilder = $modflowProcessBuilder;
    }

    public function addToQueue($modelId, $executable = 'mf2005')
    {
        if (! in_array($executable, $this->availableExecutables)){
            throw new InvalidArgumentException(sprintf('Executable %s is not available.', $executable));
        }

        $modflowCalculation = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
            ->findOneBy(array(
                'modelId' => $modelId,
                'state' => ModflowCalculation::STATE_IN_QUEUE
            ));

        if (! $modflowCalculation instanceof ModflowCalculation){
            $modflowCalculation = new ModflowCalculation();
        }

        $modflowCalculation->setModelId(Uuid::fromString($modelId));
        $modflowCalculation->setExecutable($executable);
        $this->entityManager->persist($modflowCalculation);
        $this->entityManager->flush();
    }

    public function calculate($modelId, $executable = 'mf2005')
    {
        $process = $this->modflowProcessBuilder->buildCalculationProcess($modelId, $executable);
        $process->getProcess()->run();
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow Calculation Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }

    public function getRasterResult($modelId, $layer, array $timesteps, array $stressPeriods, $operation = ModflowResultRasterParameter::OP_RAW){

        $process = $this->modflowProcessBuilder->buildRasterResultProcess($modelId, $layer, $timesteps, $stressPeriods, $operation);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow RasterResult Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }

    public function getTimeseriesResult($modelId, $layer, $row, $col, $operation = ModflowResultTimeSeriesParameter::OP_RAW){

        $process = $this->modflowProcessBuilder->buildTimeseriesResultProcess($modelId, $layer, $row, $col, $operation);
        $process->run();

        if (! $process->isSuccessful()) {
            throw new ProcessFailedException('Modflow TimeSeriesResult Process failed with ErrorMessage: '. $process->getErrorOutput());
        }

        return true;
    }
}