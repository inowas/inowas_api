<?php

namespace AppBundle\Entity;

use AppBundle\Model\Point;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity(repositoryClass="AppBundle\Repository\GeologicalUnitRepository")
 * @ORM\Table(name="geological_units")
 */
class GeologicalUnit extends SoilModelObject
{

    const TOP_LAYER = 0;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'geologicalunit';

    /**
     * @var Point
     *
     * @ORM\Column(name="geometry", type="point", nullable=true)
     */
    private $point;

    /**
     * @var integer
     *
     * @ORM\Column(name="order_number", type="integer", nullable=false)
     */
    private $order;

    /**
     * Set point
     *
     * @param point $point
     * @return $this
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

    public function getFirstPropertyValue(Property $property){
        $values = $property->getValues();

        foreach ($values as $value) {
            if ($value instanceof PropertyValue) {
                return $value->getValue();
            }
        }

        return null;
    }
    
    public function getBottomElevation()
    {
        /** @var array */
        $properties = $this->getPropertiesByPropertyTypeAbbreviation(PropertyType::BOTTOM_ELEVATION);
        if (count($properties)>0) {
            return $this->getFirstPropertyValue($properties[0]);
        }

        return null;
    }

    public function getTopElevation()
    {
        /** @var array */
        $properties = $this->getPropertiesByPropertyTypeAbbreviation(PropertyType::TOP_ELEVATION);
        if (count($properties)>0) {
            return $this->getFirstPropertyValue($properties[0]);
        }

        return null;
    }

    /**
     * @return int
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return $this
     */
    public function setOrder($order)
    {
        $this->order = $order;
        return $this;
    }
}