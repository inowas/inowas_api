<?php

namespace AppBundle\Entity;

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
}
