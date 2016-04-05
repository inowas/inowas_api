<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity()
 * @ORM\Table(name="observation_points")
 */
class ObservationPoint extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'observationPoint';

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $point;

    /**
     * @var $elevation
     *
     * @ORM\Column(name="elevation", type="float", nullable=true)
     */
    private $elevation;

    /**
     * @var ArrayCollection ModelObject
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\ModelObject", mappedBy="observationPoints")
     */
    private $modelObjects;

    /**
     * ObservationPoint constructor.
     * @param User|null $owner
     * @param Project|null $project
     * @param bool|false $public
     */
    public function __construct(User $owner = null, Project $project = null, $public = false)
    {
        parent::__construct($owner, $project, $public);

        $this->modelObjects = new ArrayCollection();
    }

    /**
     * Set point
     *
     * @param point $point
     * @return ObservationPoint
     */
    public function setPoint($point)
    {
        $this->point = $point;

        return $this;
    }

    /**
     * Get point
     *
     * @return point 
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * Set elevation
     *
     * @param float $elevation
     * @return ObservationPoint
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;

        return $this;
    }

    /**
     * Get elevation
     *
     * @return float 
     */
    public function getElevation()
    {
        return $this->elevation;
    }

    /**
     * Add modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     * @return ObservationPoint
     */
    public function addModelObject(ModelObject $modelObjects)
    {
        $this->modelObjects[] = $modelObjects;

        if (!in_array($this, $modelObjects->getObservationPoints()->toArray()))
        {
            $modelObjects->addObservationPoint($this);
        }

        return $this;
    }

    /**
     * Remove modelObjects
     *
     * @param \AppBundle\Entity\ModelObject $modelObjects
     */
    public function removeModelObject(ModelObject $modelObjects)
    {
        $this->modelObjects->removeElement($modelObjects);

        if (in_array($this, $modelObjects->getObservationPoints()->toArray()))
        {
            $modelObjects->removeObservationPoint($this);
        }
    }

    /**
     * Get modelObjects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getModelObjects()
    {
        return $this->modelObjects;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'ObservationPoint';
    }
}
