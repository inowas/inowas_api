<?php

namespace Inowas\SoilmodelBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Inowas\SoilmodelBundle\Exception\InvalidArgumentException;
use Inowas\SoilmodelBundle\Factory\SoilmodelFactory;
use Inowas\SoilmodelBundle\Model\BoreHole;
use Inowas\SoilmodelBundle\Model\Layer;
use Inowas\SoilmodelBundle\Model\Property;
use Inowas\SoilmodelBundle\Model\Soilmodel;
use Ramsey\Uuid\Uuid;

class SoilmodelManager
{
    /** @var  EntityManager $em */
    private $em;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }

    public function create(){
        return SoilmodelFactory::create();
    }

    /**
     * @param $id
     * @return Soilmodel|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->em
            ->getRepository('InowasSoilmodelBundle:Soilmodel')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param Soilmodel $soilmodel
     */
    public function update(Soilmodel $soilmodel){
        $this->em->persist($soilmodel);
        $this->em->flush();
    }

    public function readPropertyTypesFrom(Soilmodel $soilmodel, Layer $layer)
    {
        $propertyTypes = new ArrayCollection();
        $layerOrder = $layer->getOrder();

        /** @var BoreHole $boreHole */
        foreach ($soilmodel->getBoreHoles() as $boreHole){
            /** @var Layer $layer */
            foreach ($boreHole->getLayers() as $layer){
                if ($layer->getOrder() === $layerOrder){
                    /** @var Property $property */
                    foreach ($layer->getProperties() as $property){
                        if (! $propertyTypes->contains($property->getType())){
                            $propertyTypes->add($property->getType());
                        }
                    }
                }
            }
        }

        return $propertyTypes;
    }
}