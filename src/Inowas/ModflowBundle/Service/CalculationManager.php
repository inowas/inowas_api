<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\Flopy\Model\Factory\CalculationPropertiesFactory;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Calculation;
use Inowas\ModflowBundle\Model\CalculationFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Ramsey\Uuid\Uuid;

class CalculationManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * CalculationManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @param ModflowModel $model
     * @return Calculation
     */
    public function create(ModflowModel $model){
        $calculationProperties = CalculationPropertiesFactory::loadFromApiAndRun($model);
        return CalculationFactory::create($calculationProperties, $model);
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
