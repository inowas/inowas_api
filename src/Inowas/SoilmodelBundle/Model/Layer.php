<?php

namespace Inowas\SoilmodelBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ramsey\Uuid\Uuid;
use SensioLabs\AnsiConverter\Theme\Theme;

class Layer extends SoilmodelObject
{
    const TOP_LAYER = 0;

    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var integer
     */
    private $order = self::TOP_LAYER;

    /** @var ArrayCollection  */
    private $properties;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->properties = new ArrayCollection();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Layer
     */
    public function setName(string $name = null): Layer
    {
        if (is_null($name)){
            $name = '';
        }

        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Layer
     */
    public function setDescription(string $description): Layer
    {
        if (is_null($description)){
            $description = '';
        }

        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(){
        return $this->order;
    }

    /**
     * @param $order
     * @return Layer
     */
    public function setOrder(int $order): Layer
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @param Property $soilModelProperty
     * @return Layer
     */
    public function addOrReplaceProperty(Property $soilModelProperty): Layer
    {
        /** @var Property $property */
        foreach ($this->properties as $key => $property){
            if ($soilModelProperty->getType() == $property->getType()){
                $this->properties->remove($key);
                break;
            }
        }

        $this->properties[] = $soilModelProperty;
        return $this;
    }

    /**
     * @param PropertyType $propertyType
     * @return Property
     */
    public function findPropertyByType(PropertyType $propertyType): Property
    {
        /** @var Property $property */
        foreach ($this->properties as $property){
            if ($property->getType() == $propertyType){
                return $property;
            }
        }

        return null;
    }

    /**
     * @return ArrayCollection
     */
    public function getProperties(){
        return $this->properties;
    }

    /**
     * @return array
     */
    public function getPropertyTypes(){
        $types = [];
        /** @var Property $property */
        foreach ($this->properties as $property){
            if ($property->getType() instanceof PropertyType){
                array_push($types, $property->getType());
            }
        }

        return $types;
    }
}
