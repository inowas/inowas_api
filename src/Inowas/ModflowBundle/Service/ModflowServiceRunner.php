<?php

namespace Inowas\ModflowBundle\Service;

use AppBundle\Entity\ModflowCalculation;
use AppBundle\Process\Modflow\ModflowCalculationParameter;
use AppBundle\Process\Modflow\ModflowCalculationProcessConfiguration;
use AppBundle\Process\Modflow\ModflowConfigurationFileCreator;
use Inowas\PythonProcessBundle\Model\PythonProcess;
use Inowas\PythonProcessBundle\Model\PythonProcessFactory;
use AppBundle\Service\ConfigurationFileCreatorFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\KernelInterface;

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

    /** This could be the cronjob-command */
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
                    } else {
                        $modflowCalculation->setState(ModflowCalculation::STATE_FINISHED_WITH_ERRORS);
                        $modflowCalculation->setErrorOutput($process->getProcess()->getErrorOutput());
                    }

                    $this->entityManager->persist($modflowCalculation);
                    $this->entityManager->flush();
                    $this->removeProcess($process);
                }
            }

            if ($runningProcesses >= $this->numberOfParallelCalculations){
                return;
            }

            $modelsToCalculate = $this->entityManager->getRepository('AppBundle:ModflowCalculation')
                ->findBy(
                    array('state' => ModflowCalculation::STATE_IN_QUEUE),
                    array('dateTimeAddToQueue' => 'ASC'),
                    $this->numberOfParallelCalculations - $runningProcesses
                );

            if (count($modelsToCalculate) > 0){
                echo sprintf('Got %s more Jobs.'."\r\n", count($modelsToCalculate));
            }

            /** @var ModflowCalculation $modelCalculation */
            foreach ($modelsToCalculate as $modelCalculation){
                $process = $this->modflowProcessBuilder->buildCalculationProcess($modelCalculation->getModelId(), $modelCalculation->getExecutable());

                $modelCalculation->setProcessId($process->getId());
                $modelCalculation->setDateTimeStart(new \DateTime());
                $modelCalculation->setState(ModflowCalculation::STATE_RUNNING);
                $this->entityManager->persist($modelCalculation);
                $this->entityManager->flush();

                $process->getProcess()->start();
                $this->addProcess($process);
            }
            sleep(1);
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