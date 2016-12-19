<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\Flopy\Model\Factory\CalculationPropertiesFactory;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Calculation;
use Inowas\ModflowBundle\Model\CalculationFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Model\Scenario;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpKernel\KernelInterface;

class CalculationManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * @var KernelInterface $kernel
     */
    private $kernel;

    /**
     * CalculationManager constructor.
     * @param EntityManager $entityManager
     * @param KernelInterface $kernel
     */
    public function __construct(EntityManager $entityManager, KernelInterface $kernel){
        $this->entityManager = $entityManager;
        $this->kernel = $kernel;
    }

    /**
     * @param ModflowModel $model
     * @return Calculation
     */
    public function createFromModel(ModflowModel $model)
    {
        $type = 'modflow';
        $calculationProperties = CalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
        $calculation = CalculationFactory::create($calculationProperties, $model);
        $calculation->setModelId($model->getId());

        $baseUrl = $this->kernel->getContainer()->getParameter('inowas.api_base_url');
        $port = $this->kernel->getContainer()->getParameter('inowas.api_port');
        $calculationId = $calculation->getId();
        $modelId = $calculation->getModelId();
        $dataFolder = sprintf('%s/%s', $this->kernel->getContainer()->getParameter('inowas.modflow.data_folder'), $modelId);
        $calculationUrl = sprintf('%s:%d/api/%s/calculation/%s/packages.json', $baseUrl, $port, $type, $calculationId);
        $modelUrl = sprintf('%s:%d/api/%s/calculation/%s/packages/packageName.json', $baseUrl, $port, $type, $modelId);
        $submitHeadsUrl = sprintf('%s:%d/api/%s/calculation/%s/heads.json', $baseUrl, $port, $type, $modelId);

        $calculation->setDataFolder($dataFolder);
        $calculation->setCalculationUrl($calculationUrl);
        $calculation->setModelUrl($modelUrl);
        $calculation->setSubmitHeadsUrl($submitHeadsUrl);
        $calculation->setApiKey($this->kernel->getContainer()->get('inowas.modflow.toolmanager')->findApiKeyByModelId($modelId));

        return $calculation;
    }

    /**
     * @param Scenario $scenario
     * @return Calculation
     */
    public function createFromScenario(Scenario $scenario)
    {
        $baseModelId = $scenario->getBaseModelId();
        $model = $this->kernel->getContainer()->get('inowas.modflow.toolmanager')->findModelById($baseModelId);
        if (! $model instanceof ModflowModel){
            throw new InvalidArgumentException(sprintf('Model with Id=%s not available.', $baseModelId));
        }
        $type = 'scenarioanalysis';
        $model = $scenario->applyTo($model);
        $calculationProperties = CalculationPropertiesFactory::loadFromApiRunAndSubmit($model);
        $calculation = CalculationFactory::create($calculationProperties, $model);

        $scenarioId = $scenario->getId();
        $calculation->setModelId($scenarioId);
        $baseUrl = $this->kernel->getContainer()->getParameter('inowas.api_base_url');
        $port = $this->kernel->getContainer()->getParameter('inowas.api_port');
        $calculationId = $calculation->getId();

        $dataFolder = sprintf('%s/%s', $this->kernel->getContainer()->getParameter('inowas.modflow.data_folder'), $scenarioId);
        $calculationUrl = sprintf('%s:%d/api/%s/calculation/%s/packages.json', $baseUrl, $port, $type, $calculationId);
        $modelUrl = sprintf('%s:%d/api/%s/calculation/%s/packages/packageName.json', $baseUrl, $port, $type, $scenarioId);
        $submitHeadsUrl = sprintf('%s:%d/api/%s/calculation/%s/heads.json', $baseUrl, $port, $type, $scenarioId);

        $calculation->setDataFolder($dataFolder);
        $calculation->setCalculationUrl($calculationUrl);
        $calculation->setModelUrl($modelUrl);
        $calculation->setSubmitHeadsUrl($submitHeadsUrl);
        $calculation->setApiKey($this->kernel->getContainer()->get('inowas.scenarioanalysis.scenarioanalysismanager')->findApiKeyByScenarioId($scenarioId));

        return $calculation;
    }

    /**
     * @param $id
     * @return Calculation|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasModflowBundle:Calculation')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param $id
     * @return Calculation|null
     */
    public function findByModelId($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasModflowBundle:Calculation')
            ->findOneBy(array(
                'modelId' => $id,
                'state' => Calculation::STATE_IN_QUEUE
            ));
    }

    /**
     * @param Calculation $calculation
     * @return Calculation
     */
    public function update(Calculation $calculation): Calculation
    {
        $this->entityManager->persist($calculation);
        $this->entityManager->flush();
        return $calculation;
    }

    /**
     * @param Calculation $calculation
     * @return Calculation
     */
    public function remove(Calculation $calculation): Calculation
    {
        $this->entityManager->remove($calculation);
        $this->entityManager->flush();
        return $calculation;
    }
}
