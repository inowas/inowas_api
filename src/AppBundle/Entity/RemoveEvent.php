<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class RemoveEvent extends AbstractEvent
{
    /** @var ModelObject $modelObject */
    private $modelObject;

    public function __construct(ModelObject $modelObject)
    {
        parent::__construct();
        $this->modelObject = $modelObject;
    }

    /**
     * @return ModelObject
     */
    public function getElement()
    {
        return $this->modelObject;
    }

    /**
     * @param ModFlowModel $model
     * @return mixed
     */
    public function applyTo(ModFlowModel $model)
    {
        if ($model->getBoundaries() instanceof ArrayCollection){
            $this->applyToArrayCollection($model->getBoundaries(), $this->modelObject);
        }

        if ($model->getObservationPoints() instanceof ArrayCollection){
            $this->applyToArrayCollection($model->getObservationPoints(), $this->modelObject);
        }

        if ($model->getModelObjects() instanceof ArrayCollection){
            $this->applyToArrayCollection($model->getModelObjects(), $this->modelObject);
        }

        return $model;
    }

    protected function applyToArrayCollection(ArrayCollection $collection, ModelObject $modelObject){
        /** @var ModelObject $element */
        foreach ($collection as $element){
            if ($element->getId() == $modelObject->getId()){
                $collection->removeElement($modelObject);
            }
        }
    }
}
