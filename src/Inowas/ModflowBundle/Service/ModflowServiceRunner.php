<?php

namespace Inowas\ModflowBundle\Service;

use AppBundle\Entity\ModflowCalculation;
use AppBundle\Process\Modflow\ModflowCalculationParameter;
use AppBundle\Process\Modflow\ModflowCalculationProcessConfiguration;
use AppBundle\Process\Modflow\ModflowConfigurationFileCreator;
use AppBundle\Process\PythonProcess;
use AppBundle\Process\PythonProcessFactory;
use AppBundle\Service\ConfigurationFileCreatorFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpKernel\KernelInterface;

class ModflowServiceRunner
{
    /** @var EntityManager  */
    protected $entityManager;

    /** @var ArrayCollection $processes */
    protected $processes;

    /** @var int */
    protected $numberOfParallelCalculations = 5;

    public function __construct(KernelInterface $kernel, ConfigurationFileCreatorFactory $configurationFileCreatorFactory)
    {
        $this->processes = new ArrayCollection();
        $this->entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');
        $this->numberOfParallelCalculations = $kernel->getContainer()->getParameter('inowas.modflow.number_of_parallel_running_processes');
        $this->baseUrl = $kernel->getContainer()->getParameter('inowas.modflow.api_base_url');
        $this->workspace = $kernel->getContainer()->getParameter('inowas.modflow.data_folder');
        $this->kernel = $kernel;
        $this->configurationFileCreatorFactory = $configurationFileCreatorFactory;
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
                $process = $this->createCalculationProcess($modelCalculation->getModelId(), $modelCalculation->getExecutable());

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

    private function createCalculationProcess($modelId, $executable = 'mf2005'){

        $mfCalculationParams = new ModflowCalculationParameter($modelId, $this->baseUrl);

        /** @var ModflowConfigurationFileCreator $inputFileCreator */
        $inputFileCreator = $this->configurationFileCreatorFactory->create('modflow');
        $inputFileCreator->createFiles($mfCalculationParams);

        $processConfig = new ModflowCalculationProcessConfiguration($inputFileCreator->getInputFile(), $this->workspace.'/'.$modelId, $executable, $this->baseUrl);
        $processConfig->setWorkingDirectory($this->kernel->getContainer()->getParameter('inowas.modflow.working_directory'));
        return PythonProcessFactory::create($processConfig);
    }

}