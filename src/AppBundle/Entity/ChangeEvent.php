<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class ChangeEvent extends AbstractEvent
{
    /** @var ModelObject $origin */
    private $origin;

    /** @var ModelObject $changed */
    private $changed;

    public function __construct(ModelObject $origin, ModelObject $changed)
    {
        parent::__construct();
        $this->origin = $origin;
        $this->changed = $changed;
    }
}
