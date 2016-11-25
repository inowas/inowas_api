<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Area;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Ramsey\Uuid\Uuid;

class ModflowModelManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    /**
     * ModflowModelManager constructor.
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @return ModflowModel
     */
    public function create(){
        return ModflowModelFactory::create();
    }

    /**
     * @param $id
     * @return ModFlowModel|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasModflowBundle:ModflowModel')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @return array
     */
    public function findAll(){
        return $this->entityManager
            ->getRepository('InowasModflowBundle:ModflowModel')
            ->findAll();
    }

    /**
     * @param $id
     * @return Area
     */
    public function findAreaByModelId($id)
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        $model = $this->findById($id);

        if (!$model->getArea() instanceof Area) {
            throw new InvalidArgumentException(sprintf('ModelArea of Model with id=%s not found', $id));
        }

        return $model->getArea();
    }

    /**
     * @param ModFlowModel $model
     * @return ModFlowModel
     */
    public function update(ModflowModel $model)
    {
        $this->entityManager->persist($model);
        $this->entityManager->flush();
        return $model;
    }

    /**
     * @param Area $area
     * @return Area
     */
    public function updateArea(Area $area)
    {
        $this->entityManager->persist($area);
        $this->entityManager->flush();
        return $area;
    }

    /**
     * @param ModFlowModel $model
     */
    public function remove(ModflowModel $model){
        $this->entityManager->remove($model);
        $this->entityManager->flush();
    }
}
