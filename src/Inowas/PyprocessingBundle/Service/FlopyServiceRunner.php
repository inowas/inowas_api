<?php

namespace Inowas\PyprocessingBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\Calculation;
use Inowas\PyprocessingBundle\Model\ProcessWrapper;


/**
 * Class ModflowServiceRunner
 * @package Inowas\PyprocessingBundle\Service
 *
 * @codeCoverageIgnore
 */
class FlopyServiceRunner
{
    /** @var EntityManager  */
    protected $entityManager;

    /** @var  Flopy */
    protected $flopy;

    /** @var ArrayCollection $processes */
    protected $processes;

    /** @var int */
    protected $numberOfParallelCalculations;


    public function __construct(EntityManager $entityManager, Flopy $flopy, $numberOfParallelCalculations = 5)
    {
        $this->processes = new ArrayCollection();
        $this->entityManager = $entityManager;
        $this->flopy = $flopy;
        $this->numberOfParallelCalculations = $numberOfParallelCalculations;
    }

    /**
     * @param bool $asDaemon
     */
    public function run($asDaemon = false){

        /**
         * Reset not finished jobs
         */
        $this->cleanUp();

        echo sprintf('Waiting for Jobs.'."\r\n");

        while (1){
            $runningProcesses = 0;
            /** @var ProcessWrapper $processWrapper */
            foreach ($this->processes as $processWrapper){
                $process = $processWrapper->getProcess();
                if ($process->isRunning()){
                    $runningProcesses++;
                    continue;
                }

                if (! $process->isRunning()){
                    $modflowCalculation = $this->entityManager
                        ->getRepository('InowasModflowBundle:Calculation')
                        ->findOneBy(array(
                            'id' => $processWrapper->getId()
                        ));

                    $modflowCalculation->setDateTimeEnd(new \DateTime());
                    if ($process->isSuccessful()){
                        $modflowCalculation->setState(Calculation::STATE_FINISHED_SUCCESSFUL);
                        $modflowCalculation->setFinishedWithSuccess(true);
                        $modflowCalculation->setOutput($modflowCalculation->getOutput().$process->getOutput());
                        echo sprintf("Process end:\r\n Message: \r\n %s", $process->getOutput());
                    } else {
                        $modflowCalculation->setState(Calculation::STATE_FINISHED_WITH_ERRORS);
                        $modflowCalculation->setFinishedWithSuccess(false);
                        $modflowCalculation->setOutput($modflowCalculation->getOutput().$process->getErrorOutput());
                        echo sprintf("Process ended up with error:\r\n ErrorMessage: \r\n %s", $process->getErrorOutput());
                    }

                    $this->entityManager->persist($modflowCalculation);
                    $this->entityManager->flush($modflowCalculation);
                    $this->removeProcess($processWrapper);
                }
            }

            if ($runningProcesses >= $this->numberOfParallelCalculations){
                continue;
            }

            $modelsToCalculate = $this->entityManager->getRepository('InowasModflowBundle:Calculation')
                ->findBy(
                    array('state' => Calculation::STATE_IN_QUEUE),
                    array('dateTimeAddToQueue' => 'ASC'),
                    $this->numberOfParallelCalculations - $runningProcesses
                );

            if (count($modelsToCalculate) == 0 && $asDaemon === false && $runningProcesses == 0){
                echo sprintf('There are no more jobs in the queue. Leaving...'."\r\n");
                return;
            }

            if (count($modelsToCalculate) > 0){
                echo sprintf('Got %s more Jobs.'."\r\n", count($modelsToCalculate));
            }

            /** @var Calculation $modelCalculation */
            foreach ($modelsToCalculate as $modelCalculation){

                $process = $this->flopy->calculate($modelCalculation, true);
                $modelCalculation->setDateTimeStart(new \DateTime());
                $modelCalculation->setState(Calculation::STATE_RUNNING);
                $modelCalculation->setOutput($modelCalculation->getOutput().sprintf("Calculation started at %s...\r\n", (new \DateTime('now'))->format('Y-m-d H:i:s')));
                $this->entityManager->persist($modelCalculation);
                $this->entityManager->flush($modelCalculation);


                $processWrapper = new ProcessWrapper($process, $modelCalculation->getId());
                $this->addProcess($processWrapper);
                $process->start();
            }
        }
    }

    private function addProcess(ProcessWrapper $processWrapper)
    {
        $this->processes->add($processWrapper);
    }

    private function removeProcess(ProcessWrapper $processWrapper)
    {
        if ($this->processes->contains($processWrapper)){
            $this->processes->removeElement($processWrapper);
        }
    }

    private function cleanUp(){
        $startedButNotFinishedJobs = $this->entityManager->getRepository('InowasModflowBundle:Calculation')
            ->findBy(array('state' => Calculation::STATE_RUNNING));

        foreach ($startedButNotFinishedJobs as $startedButNotFinishedJob){
            $startedButNotFinishedJob->setState(Calculation::STATE_IN_QUEUE);
            $this->entityManager->persist($startedButNotFinishedJob);
            echo sprintf('Reset started but not fished Job %s'."\r\n", $startedButNotFinishedJob->getId()->toString());
        }

        $this->entityManager->flush();
    }
}
