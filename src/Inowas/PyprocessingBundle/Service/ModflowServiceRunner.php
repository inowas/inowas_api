<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Entity\ModflowCalculation;
use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcess;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;

/**
 * Class ModflowServiceRunner
 * @package Inowas\PyprocessingBundle\Service
 *
 * @codeCoverageIgnore
 */
class ModflowServiceRunner
{
    /** @var EntityManager  */
    protected $entityManager;

    /** @var  ModflowProcessBuilder */
    protected $modflowProcessBuilder;

    /** @var ArrayCollection $processes */
    protected $processes;

    /** @var int */
    protected $numberOfParallelCalculations;


    public function __construct(EntityManager $entityManager,  ModflowProcessBuilder $modflowProcessBuilder, $numberOfParallelCalculations = 5)
    {
        $this->processes = new ArrayCollection();
        $this->entityManager = $entityManager;
        $this->modflowProcessBuilder = $modflowProcessBuilder;
        $this->numberOfParallelCalculations = $numberOfParallelCalculations;
    }

    /** This could be the cronJob-command */
    public function run(){

        echo sprintf('Waiting for Jobs.'."\r\n");

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

            if (count($modelsToCalculate) == 0){
                echo sprintf('There are no more jobs in the queue. Leaving...'."\r\n");
                return;
            }

            if (count($modelsToCalculate) > 0){
                echo sprintf('Got %s more Jobs.'."\r\n", count($modelsToCalculate));
            }

            /** @var ModflowCalculation $modelCalculation */
            foreach ($modelsToCalculate as $modelCalculation){
                $process = $this->modflowProcessBuilder->getCalculationProcess($modelCalculation->getModelId(), $modelCalculation->getExecutable());

                $modelCalculation->setProcessId($process->getId());
                $modelCalculation->setDateTimeStart(new \DateTime());
                $modelCalculation->setState(ModflowCalculation::STATE_RUNNING);
                $this->entityManager->persist($modelCalculation);
                $this->entityManager->flush();

                $process->getProcess()->start();
                $this->addProcess($process);
            }
            sleep(10);
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