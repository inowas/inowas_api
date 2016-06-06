<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeologicalLayerRepository")
 * @ORM\Table(name="geological_layers")
 */
class GeologicalLayer extends SoilModelObject
{

    const TOP_LAYER = 0;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist", "soilmodellayers"})
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
     * @var integer
     *
     * @ORM\Column(name="order_number", type="integer", nullable=false)
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist", "soilmodellayers"})
     */
    private $order;

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
        if (!$this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits[] = $geologicalUnit;
        }

        return $this;
    }

    /**
     * Remove geologicalUnit
     *
     * @param GeologicalUnit $geologicalUnit
     * @return $this
     */
    public function removeGeologicalUnit(GeologicalUnit $geologicalUnit)
    {
        if ($this->geologicalUnits->contains($geologicalUnit)) {
            $this->geologicalUnits->removeElement($geologicalUnit);
        }

        return $this;
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

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;

        return $this;
    }


}
