<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeologicalLayerRepository")
 * @ORM\Table(name="geological_layers")
 */
class GeologicalLayer extends ModelObject
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'geologicallayer';


    /**
     * @var ArrayCollection GeologicalUnit
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalUnit")
     * @ORM\JoinTable(name="geological_layers_geological_units",
     *     joinColumns={@ORM\JoinColumn(name="geological_layer_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="geological_unit_id", referencedColumnName="id")}
     *     )
     * @JMS\Type("ArrayCollection<AppBundle\Entity\GeologicalUnit>"))
     * @JMS\Groups({"modelobjectdetails", "soilmodeldetails"})
     **/
    private $geologicalUnits;

    /**
     * Layer constructor.
     * @param User|null $owner
     * @param bool|false $public
     */
    public function __construct(User $owner = null, $public = false)
    {
        parent::__construct($owner, $public);

        $this->geologicalUnits = new ArrayCollection();
    }

    /**
     * Add geologicalUnit
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnit
     * @return GeologicalLayer
     */
    public function addGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits[] = $geologicalUnit;
        return $this;
    }

    /**
     * Remove geologicalUnit
     *
     * @param \AppBundle\Entity\GeologicalUnit $geologicalUnit
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        $this->geologicalUnits->removeElement($geologicalUnit);
    }

    /**
     * Get geologicalUnits
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGeologicalUnits()
    {
        return $this->geologicalUnits;
    }
}
