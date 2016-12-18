<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Area;
use Inowas\ModflowBundle\Model\Modflow;
use Inowas\ModflowBundle\Model\ModflowFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ModflowBundle\Model\ModflowModelFactory;
use Ramsey\Uuid\Uuid;

class ModflowToolManager
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
     * @return Modflow
     */
    public function create(){
        return ModflowFactory::create();
    }

    /**
     * @param Modflow $modflow
     * @return Modflow
     */
    public function update(Modflow $modflow){
        $this->entityManager->persist($modflow);
        $this->entityManager->flush();
        return $modflow;
    }

    /**
     * @param $modelId
     * @return Uuid
     */
    public function findApiKeyByModelId($modelId){
        $model = $this->entityManager->getRepository('InowasModflowBundle:ModflowModel')
            ->findOneBy(array(
                'id' => $modelId
            ));

        $modflowTool = $this->entityManager->getRepository('InowasModflowBundle:Modflow')
           ->findOneBy(array(
              'modflowModel' => $model
           ));

        $userId = $modflowTool->getUserId();

        $user = $this->entityManager->getRepository('InowasAppBundle:User')
            ->findOneBy(array(
                'id' => $userId
            ));

        return $user->getApiKey();
    }

    /**
     * @return ModflowModel
     */
    public function createModel(){
        return ModflowModelFactory::create();
    }

    /**
     * @param $id
     * @return ModFlowModel|null
     */
    public function findModelById($id){
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
    public function findAllModels(){
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

        $model = $this->findModelById($id);

        if (!$model->getArea() instanceof Area) {
            throw new InvalidArgumentException(sprintf('ModelArea of Model with id=%s not found', $id));
        }

        return $model->getArea();
    }

    /**
     * @param ModFlowModel $model
     * @return ModFlowModel
     */
    public function updateModel(ModflowModel $model)
    {
        $this->entityManager->persist($model);
        $this->entityManager->flush();
        return $model;
    }

    /**
     * @param Area $area
     * @return Area
     */
    public function updateModelArea(Area $area)
    {
        $this->entityManager->persist($area);
        $this->entityManager->flush();
        return $area;
    }

    /**
     * @param ModFlowModel $model
     */
    public function removeModel(ModflowModel $model){
        $this->entityManager->remove($model);
        $this->entityManager->flush();
    }
}
