<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ChangeBoundaryEvent extends ChangeEvent
{
    /**
     * @var ModelObject $origin
     *
     * @ORM\ManyToOne(targetEntity="ModelObject")
     */
    private $origin;

    /**
     * @var ModelObject $newBoundary
     *
     * @ORM\ManyToOne(targetEntity="ModelObject", cascade={"persist", "remove"})
     */
    private $newBoundary;


    public function __construct(BoundaryModelObject $origin, BoundaryModelObject $newBoundary)
    {
        parent::__construct();
        $this->origin = $origin;
        $this->newBoundary = $newBoundary;
    }

    /**
     * @return BoundaryModelObject|ModelObject
     */
    public function getOrigin(){
        return $this->origin;
    }

    /**
     * @return BoundaryModelObject|ModelObject
     */
    public function getNewBoundary(){
        return $this->newBoundary;
    }

    /**
     * @param ModFlowModel $model
     * @return ModFlowModel
     */
    public function applyTo(ModFlowModel $model)
    {
        if ($this->getOrigin() instanceof BoundaryModelObject && $this->getNewBoundary() instanceof BoundaryModelObject){

            /** @var BoundaryModelObject $boundary */
            foreach ($model->getBoundaries()->toArray() as $bKey => $boundary){
                if ($boundary->getId() == $this->getOrigin()->getId()){
                    $model->removeBoundary($boundary);
                    $model->addBoundary($this->getNewBoundary()->setMutable($this->isMutable()));
                }
            }
        }
        return $model;
    }
}
