<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class AddBoundaryEvent extends AddEvent
{
    /**
     * @var ModelObject $boundary
     *
     * @ORM\ManyToOne(targetEntity="ModelObject", cascade={"persist", "remove"})
     */
    private $boundary;

    public function __construct(BoundaryModelObject $boundary)
    {
        parent::__construct();
        $this->boundary = $boundary;
    }

    /**
     * @return BoundaryModelObject
     */
    public function getBoundary()
    {
        return $this->boundary;
    }

    /**
     * @param ModFlowModel $model
     */
    public function applyTo(ModFlowModel $model)
    {
        $model->addBoundary($this->getBoundary()->setMutable($this->isMutable()));
    }
}
