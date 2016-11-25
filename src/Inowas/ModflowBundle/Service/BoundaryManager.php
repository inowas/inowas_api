<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\ModflowBundle\Model\Boundary\BoundaryInterface;
use Inowas\ModflowBundle\Model\BoundaryFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Ramsey\Uuid\Uuid;

class BoundaryManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $type
     * @return BoundaryInterface
     */
    public function create(string $type){
        return BoundaryFactory::create($type);
    }

    /**
     * @param $id
     * @return BoundaryInterface|null
     */
    public function findByModelId($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        $model = $this->entityManager
            ->getRepository('InowasModflowBundle:ModflowModel')
            ->findOneBy(array(
                'id' => $id
            ));

        if (! $model instanceof ModflowModel){
            return null;
        }

        return $model->getBoundaries();
    }

    /**
     * @param $id
     * @return BoundaryInterface|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasModflowBundle:Boundary\Boundary')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param Boundary $boundary
     * @return BoundaryInterface
     */
    public function update(Boundary $boundary){
        $this->entityManager->persist($boundary);
        $this->entityManager->flush();
        return $boundary;
    }

    /**
     * @param Boundary $boundary
     * @return BoundaryInterface
     */
    public function remove(Boundary $boundary){
        $this->entityManager->remove($boundary);
        $this->entityManager->flush();
        return $boundary;
    }
}
