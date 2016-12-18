<?php

namespace AppBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Model\Head;
use Inowas\ModflowBundle\Model\ModflowModelInterface;
use Ramsey\Uuid\Uuid;

class HeadsService
{

    /** @var  EntityManager */
    protected $entityManager;

    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param ModflowModelInterface $model
     * @param int $totim
     * @param int $layer
     * @param array $data
     * @return Head
     */
    public function addHead(ModflowModelInterface $model, int $totim, int $layer, array $data){

        $head = $this->entityManager->getRepository('InowasModflowBundle:Head')
            ->findOneBy(array(
                'modelId' => $model->getId(),
                'totim' => $totim,
                'layer' => $layer
            ));

        if (! $head instanceof Head){
            $head = new Head();
        }

        $head->setModelId($model->getId());
        $head->setTotim($totim);
        $head->setLayer($layer);
        $head->setData($data);

        $this->entityManager->persist($head);
        $this->entityManager->flush();

        return $head;
    }

    /**
     * @param ModflowModelInterface $model
     * @param int $totim
     * @param int $layer
     * @return Head|null
     */
    public function getHead(ModflowModelInterface $model, int $totim, int $layer){

        return $this->entityManager
            ->getRepository('AppBundle:Head')
            ->findOneBy(array(
                'modelId' => $model->getId(),
                'totim' => $totim,
                'layer' => $layer
            ));
    }

    /**
     * @param Uuid $id
     * @return Head|null
     */
    public function getHeadById(Uuid $id){

        return $this->entityManager
            ->getRepository('AppBundle:Head')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param ModflowModelInterface $model
     * @param int $totim
     */
    public function removeHeadsFromModel(ModflowModelInterface $model, int $totim=null){
        if (is_null($totim)){
            $heads = $this->entityManager
                ->getRepository('AppBundle:Head')
                ->findBy(array(
                    'modelId' => $model->getId()
                ));
        } else {
            $heads = $this->entityManager
                ->getRepository('AppBundle:Head')
                ->findBy(array(
                    'modelId' => $model->getId(),
                    'totim' => $totim
                ));
        }

        foreach ($heads as $head) {
            $this->entityManager->remove($head);
        }

        $this->entityManager->flush();
    }

    /**
    /**
     * @param ModflowModelInterface $model
     * @return array
     */
    public function getList(ModflowModelInterface $model){

        return $this->entityManager
            ->getRepository('AppBundle:Head')
            ->findBy(
                array('modelId' => $model->getId()),
                array('totim' => 'ASC', 'layer' => 'ASC')
            );
    }
}
