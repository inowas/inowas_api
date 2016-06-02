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
class GeologicalUnit extends ModelObject
{
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
     * Set point
     *
     * @param point $point
     * @return GeologicalPoint
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

    protected function getFirstPropertyValue(Property $property){
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
}