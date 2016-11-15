<?php

namespace Inowas\Soilmodel\Service;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationConfiguration;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationResult;
use Inowas\PyprocessingBundle\Service\Interpolation;
use Inowas\Soilmodel\Exception\InvalidArgumentException;
use Inowas\Soilmodel\Model\BoreHole;
use Inowas\Soilmodel\Model\Layer;
use Inowas\Soilmodel\Model\Property;
use Inowas\Soilmodel\Model\PropertyType;
use Inowas\Soilmodel\Model\Soilmodel;
use Inowas\Soilmodel\Model\SoilmodelFactory;
use Inowas\SoilmodelBundle\Model\PointValue;

class SoilmodelManager
{
    /** @var  EntityManager $em */
    private $em;

    /** @var  Interpolation */
    private $interpolation;

    public function __construct(EntityManager $entityManager, Interpolation $interpolation)
    {
        $this->em = $entityManager;
        $this->interpolation = $interpolation;
    }

    public function create(){
        return SoilmodelFactory::create();
    }

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

    public function interpolate(Soilmodel $soilmodel, Layer $layer, PropertyType $propertyType, array $algorithms)
    {
        if (!$soilmodel->getLayers()->contains($layer)) {
            throw new InvalidArgumentException(sprintf('Layer with id %s is not a layer from Soilmodel id %s', $layer->getId(), $soilmodel->getId()));
        }

        if (! $soilmodel->getGridSize() instanceof GridSize){
            throw new InvalidArgumentException('The GridSize is not given');
        }

        if (! $soilmodel->getBoundingBox() instanceof BoundingBox){
            throw new InvalidArgumentException('The BoundingBox is not given');
        }

        $layerOrder = $layer->getOrder();
        $pointValues = array();

        /** @var BoreHole $boreHole */
        foreach ($soilmodel->getBoreHoles() as $boreHole) {
            /** @var Layer $layer */
            foreach ($boreHole->getLayers() as $layer) {
                if ($layer->getOrder() == $layerOrder) {
                    /** @var Property $property */
                    foreach ($layer->getProperties() as $property) {
                        if ($property->getType() == $propertyType) {
                            $pointValues[] = new PointValue($boreHole->getPoint(), $property->getValue()->getValue());
                        }
                    }
                }
            }
        }

        $interpolationParameter = new InterpolationConfiguration($soilmodel->getGridSize(), $soilmodel->getBoundingBox(), $pointValues, $algorithms);
        $result = $this->interpolation->interpolate($interpolationParameter);

        if (! $result instanceof InterpolationResult) {
            throw new InvalidArgumentException('The result is not valid');
        }

        $property = new Property($propertyType, $result->getData());
        $layer->addOrReplaceProperty($property);
        $this->em->persist($soilmodel);
        $this->em->flush();
    }
}