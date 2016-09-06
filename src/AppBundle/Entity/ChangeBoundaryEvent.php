<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ChangeBoundaryEvent extends AbstractEvent
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
}
