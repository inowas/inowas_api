<?php

namespace Inowas\PyprocessingBundle\Service;

use AppBundle\Exception\ProcessFailedException;
use Inowas\ModflowBundle\Model\Calculation;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Service\CalculationManager;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\ProcessBuilder;

/**
 * Class Flopy
 * @package AppBundle\Service
 *
 * @codeCoverageIgnore
 */
class Flopy
{
    /** @var  CalculationManager */
    protected $cm;

    /** @var Kernel */
    protected $kernel;

    /**
     * Flopy constructor.
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
        $this->kernel = $kernel->getContainer();
        $this->cm = $kernel->getContainer()->get('inowas.modflow.calculationmanager');
    }

    public function addModelToQueue(ModflowModel $model)
    {
        $calculation = $this->cm->createFromModel($model);
        $calculation->setOutput("Model added to queue.\r\nThe Calculation starts soon...\r\n");
        $this->cm->update($calculation);

        return $calculation;
    }

    public function addScenarioToQueue(Scenario $scenario)
    {
        $calculation = $this->cm->createFromScenario($scenario);
        $calculation->setOutput("Scenario added to queue.\r\nThe Calculation starts soon...\r\n");
        $this->cm->update($calculation);
        return $calculation;
    }

    /**
     * @param Calculation $calculation
     * @param bool $returnProcess
     * @return Process|Calculation
     * @throws ProcessFailedException
     */
    public function calculate(Calculation $calculation, $returnProcess=false)
    {
        $executable = $this->kernel->getParameter('inowas.python.executable');
        $rootDirectory = $this->kernel->getParameter('kernel.root_dir');
        $pyProcessingFolder = $this->kernel->getParameter('inowas.pyprocessing_folder');
        $scriptName = 'FlopyCalculation.py';
        $dataFolder = $calculation->getDataFolder();
        $calculationUrl = $calculation->getCalculationUrl();
        $modelUrl = $calculation->getModelUrl();
        $submitHeadsUrl = $calculation->getSubmitHeadsUrl();
        $apiKey = $calculation->getApiKey();

        $processBuilder = new ProcessBuilder();
        $processBuilder->setWorkingDirectory('../'.$rootDirectory);
        $processBuilder->add('');
        $process = $processBuilder->getProcess();

        $process->setCommandLine(sprintf(
            '\'%s\' \'%s/flopy/%s\' \'%s\' \'%s\' \'%s\' \'%s\' \'%s\'',
            $executable,
            $pyProcessingFolder,
            $scriptName,
            $dataFolder,
            $calculationUrl,
            $modelUrl,
            $submitHeadsUrl,
            $apiKey
        ));

        if ($returnProcess){
            return $process;
        }

        if ($process->isSuccessful()){
            $calculation->setOutput($process->getOutput());
            $calculation->setFinishedWithSuccess(true);
        } else {
            $calculation->setOutput($process->getErrorOutput());
            $calculation->setFinishedWithSuccess(false);
        }

        $this->cm->update($calculation);
        return $calculation;
    }

    /**
     * @return int
     */
    public function startAsyncFlopyProcessRunner()
    {
        $kernelRootDir = $this->kernel->getRootDir();
        $process = ProcessBuilder::create()
            ->setWorkingDirectory($kernelRootDir)
            ->add("")
            ->getProcess();

        $process->setCommandLine('../bin/console inowas:flopy:process:runner >> ../var/logs/flopy.log');
        $process->start();

        return 1;
    }
}
