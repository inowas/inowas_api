<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\Flopy\Model\Factory\CalculationPropertiesFactory;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Calculation;
use Inowas\ModflowBundle\Model\CalculationFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
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
     * @param string $type
     * @return Calculation
     */
    public function create(ModflowModel $model, $type = 'modflow')
    {
        switch ($type){
            case ('modflow'):
                $type = 'modflow';
                break;
            case ('scenarioanalysis'):
                $type = 'scenarioanalysis';
                break;
        }

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
