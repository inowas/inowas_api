<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Entity\ModflowCalculation;
use AppBundle\Entity\User;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcess;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

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


    public function __construct(EntityManager $entityManager,  Flopy $flopy, $numberOfParallelCalculations = 5)
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

        $startedButNotFinishedJobs = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
            ->findBy(
                array('state' => ModflowCalculation::STATE_RUNNING)
            );

        foreach ($startedButNotFinishedJobs as $startedButNotFinishedJob){
            $startedButNotFinishedJob->setState(ModflowCalculation::STATE_IN_QUEUE);
            $this->entityManager->persist($startedButNotFinishedJob);
            echo sprintf('Reset started but not fished Job %s'."\r\n", $startedButNotFinishedJob->getId()->toString());
        }

        echo sprintf('Waiting for Jobs.'."\r\n");
        $this->entityManager->flush();

        while (1){
            $runningProcesses = 0;
            /** @var PythonProcess $process */
            foreach ($this->processes as $process){
                if ($process->isRunning()){
                    $runningProcesses++;
                    continue;
                }

                if (! $process->isRunning()){
                    $modflowCalculation = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
                        ->findOneBy(array(
                            'processId' => $process->getId()
                        ));
                    $modflowCalculation->setDateTimeEnd(new \DateTime());

                    if ($process->getProcess()->isSuccessful()){
                        $modflowCalculation->setState(ModflowCalculation::STATE_FINISHED_SUCCESSFUL);
                        $modflowCalculation->setOutput($process->getProcess()->getOutput());
                        echo sprintf("Process end:\r\n Message: \r\n %s", $process->getProcess()->getOutput());
                    } else {
                        $modflowCalculation->setState(ModflowCalculation::STATE_FINISHED_WITH_ERRORS);
                        $modflowCalculation->setErrorOutput($process->getProcess()->getErrorOutput());
                        echo sprintf("Process ended up with error:\r\n ErrorMessage: \r\n %s", $process->getProcess()->getErrorOutput());
                    }

                    $this->entityManager->persist($modflowCalculation);
                    $this->entityManager->flush();
                    $this->removeProcess($process);
                }
            }

            if ($runningProcesses >= $this->numberOfParallelCalculations){
                continue;
            }

            $modelsToCalculate = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
                ->findBy(
                    array('state' => ModflowCalculation::STATE_IN_QUEUE),
                    array('dateTimeAddToQueue' => 'ASC'),
                    $this->numberOfParallelCalculations - $runningProcesses
                );

            if (count($modelsToCalculate) == 0 && $asDaemon == false && $runningProcesses == 0){
                echo sprintf('There are no more jobs in the queue. Leaving...'."\r\n");
                return;
            }

            if (count($modelsToCalculate) > 0){
                echo sprintf('Got %s more Jobs.'."\r\n", count($modelsToCalculate));
            }

            /** @var ModflowCalculation $modelCalculation */
            foreach ($modelsToCalculate as $modelCalculation){

                $user = $this->entityManager->getRepository('AppBundle:User')
                    ->findOneBy(array(
                        'id' => $modelCalculation->getUserId()
                    ));

                if (! $user instanceof User){
                    throw new InvalidArgumentException(sprintf('UserId %s not valid or user not found.', $modelCalculation->getUserId()));
                }

                $process = $this->flopy->calculate(
                    $modelCalculation->getBaseUrl(),
                    $modelCalculation->getDataFolder(),
                    $modelCalculation->getModelId()->toString(),
                    $user->getApiKey(), true
                );

                $modelCalculation->setProcessId($process->getId());
                $modelCalculation->setDateTimeStart(new \DateTime());
                $modelCalculation->setState(ModflowCalculation::STATE_RUNNING);
                $this->entityManager->persist($modelCalculation);
                $this->entityManager->flush();

                $process->getProcess()->start();
                $this->addProcess($process);
            }
        }
    }

    private function addProcess(PythonProcess $process)
    {
        $this->processes->add($process);
    }

    private function removeProcess(PythonProcess $process)
    {
        if ($this->processes->contains($process)){
            $this->processes->removeElement($process);
        }
    }
}